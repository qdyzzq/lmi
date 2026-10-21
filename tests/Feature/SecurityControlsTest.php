<?php

namespace Tests\Feature;

use App\Http\Middleware\StripHtmlInput;
use App\Models\User;
use App\Support\HtmlSanitizer;
use App\Support\SafeError;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Covers the security controls: 2FA/OTP, rate limiting, HTTPS/headers,
 * input sanitization, security logging, password policy and CSRF.
 */
class SecurityControlsTest extends TestCase
{
    use RefreshDatabase;

    protected const STRONG_PASSWORD = 'Str0ng!Passw0rd#2026';

    protected string $securityLog;

    /** The full migration set is MySQL-specific; only the auth tables are needed here. */
    protected function migrateFreshUsing()
    {
        return [
            '--drop-views' => false,
            '--path' => [
                'database/migrations/0001_01_01_000000_create_users_table.php',
                'database/migrations/0001_01_01_000001_create_cache_table.php',
                'database/migrations/2026_04_13_000001_add_role_to_users_table.php',
                'database/migrations/2026_04_13_000056_add_phone_number_to_user.php',
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->securityLog = storage_path('logs/security-test.log');
        @unlink($this->securityLog);
        config([
            'logging.channels.security' => ['driver' => 'single', 'path' => $this->securityLog, 'level' => 'info'],
            'security.force_https' => false,
        ]);
    }

    protected function tearDown(): void
    {
        @unlink($this->securityLog);

        parent::tearDown();
    }

    protected function makeUser(string $role = 'admin', string $password = self::STRONG_PASSWORD): User
    {
        return User::create([
            'name'         => ucfirst($role),
            'email'        => $role . '@example.test',
            'phone_number' => '639000000000',
            'role'         => $role,
            'password'     => Hash::make($password),
        ]);
    }

    /** Fakes the SMS gateway and returns a closure that yields the last OTP sent. */
    protected function fakeSms(): \Closure
    {
        Http::fake(['*' => Http::response('OK', 200)]);

        return function () {
            $code = null;
            Http::assertSent(function (HttpRequest $request) use (&$code) {
                if (preg_match('/is (\d{6})\./', urldecode($request->url()), $m)) {
                    $code = $m[1];
                }
                return true;
            });
            return $code;
        };
    }

    protected function securityLogContains(string $event): bool
    {
        return is_file($this->securityLog) && str_contains(file_get_contents($this->securityLog), $event);
    }

    // ==================== 1. AUTHENTICATION WITH 2FA ====================

    public function test_correct_password_requires_otp_and_otp_is_stored_hashed(): void
    {
        $lastOtp = $this->fakeSms();
        $user = $this->makeUser();

        $this->post('/login', ['email' => $user->email, 'password' => self::STRONG_PASSWORD])
            ->assertRedirect(route('otp'));

        $this->assertGuest();
        $this->assertNull(session('otp'), 'Plain-text OTP must not be stored in the session');
        $this->assertTrue(Hash::check($lastOtp(), session('otp_hash')));

        // Protected pages stay closed until the OTP is entered.
        $this->get('/admin/JobMarketDemandsForm')->assertRedirect();
        $this->assertGuest();
    }

    public function test_valid_otp_logs_the_user_in(): void
    {
        $lastOtp = $this->fakeSms();
        $user = $this->makeUser();

        $this->post('/login', ['email' => $user->email, 'password' => self::STRONG_PASSWORD]);
        $this->post('/otp/verify', ['otp' => $lastOtp()])
            ->assertRedirect(route('admin.job.Market.Demands.Form'));

        $this->assertAuthenticatedAs($user);
        $this->assertTrue($this->securityLogContains('auth.login_success'));
    }

    public function test_otp_expires(): void
    {
        $lastOtp = $this->fakeSms();
        $user = $this->makeUser();

        $this->post('/login', ['email' => $user->email, 'password' => self::STRONG_PASSWORD]);

        $this->travel(11)->minutes();

        $this->post('/otp/verify', ['otp' => $lastOtp()])->assertSessionHasErrors('otp');
        $this->assertGuest();
    }

    public function test_too_many_wrong_otps_cancel_the_login(): void
    {
        $lastOtp = $this->fakeSms();
        $user = $this->makeUser();

        $this->post('/login', ['email' => $user->email, 'password' => self::STRONG_PASSWORD]);
        $good = $lastOtp();
        $bad  = $good === '111111' ? '222222' : '111111';

        for ($i = 1; $i < 5; $i++) {
            $this->post('/otp/verify', ['otp' => $bad])->assertSessionHasErrors('otp');
        }
        $this->post('/otp/verify', ['otp' => $bad])->assertRedirect(route('login'));

        // Even the right code no longer works: the user must start over.
        $this->post('/otp/verify', ['otp' => $good])->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertTrue($this->securityLogContains('auth.otp_locked_out'));
    }

    public function test_failed_sms_allows_immediate_email_fallback_but_resends_are_throttled(): void
    {
        Http::fake(['*' => Http::response('gateway down', 500)]);
        \Illuminate\Support\Facades\Mail::fake();
        $user = $this->makeUser();

        $this->post('/login', ['email' => $user->email, 'password' => self::STRONG_PASSWORD])
            ->assertSessionHas('error');

        // SMS failed, so email can be requested right away...
        $this->post('/otp/resend', ['via' => 'email'])->assertSessionHas('success');
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\OtpMail::class, 1);

        // ...but a second resend straight after is refused (cooldown).
        $this->post('/otp/resend', ['via' => 'email'])->assertSessionHas('error');
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\OtpMail::class, 1);
    }

    public function test_sms_gateway_is_called_with_tls_verification(): void
    {
        $this->fakeSms();
        $user = $this->makeUser();

        $this->post('/login', ['email' => $user->email, 'password' => self::STRONG_PASSWORD]);

        $this->assertNotFalse(config('sms.verify'));
        Http::assertSentCount(1);
    }

    // ==================== 2. RATE LIMITING / LOCKOUT ====================

    public function test_account_is_locked_after_repeated_wrong_passwords(): void
    {
        $this->fakeSms();
        $user = $this->makeUser();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);
        }

        // Correct password is refused while locked.
        $this->post('/login', ['email' => $user->email, 'password' => self::STRONG_PASSWORD])
            ->assertSessionHasErrors('email');
        $this->assertNull(session('otp_hash'));
        $this->assertTrue($this->securityLogContains('auth.account_locked'));
    }

    public function test_login_endpoint_is_rate_limited_per_ip(): void
    {
        $this->fakeSms();

        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', ['email' => "user{$i}@example.test", 'password' => 'x']);
        }

        $this->post('/login', ['email' => 'another@example.test', 'password' => 'x'])
            ->assertSessionHas('error', 'Too many requests. Please wait a minute and try again.');
        $this->assertTrue($this->securityLogContains('request.rate_limited'));
    }

    public function test_api_routes_are_rate_limited(): void
    {
        config(['security.rate_limits.api' => 2]);
        Route::middleware('api')->get('/api/__rate-test', fn () => ['ok' => true]);

        $this->getJson('/api/__rate-test')->assertOk()->assertHeader('X-RateLimit-Limit', 2);
        $this->getJson('/api/__rate-test')->assertOk();
        $this->getJson('/api/__rate-test')->assertStatus(429);
    }

    // ==================== 3. HTTPS / TLS ====================

    public function test_http_is_redirected_to_https_when_enforced(): void
    {
        config(['security.force_https' => true]);

        $this->get('http://lmi.example.gov.ph/login')
            ->assertStatus(301)
            ->assertRedirect('https://lmi.example.gov.ph/login');
    }

    public function test_no_redirect_loop_behind_https_proxy(): void
    {
        config(['security.force_https' => true]);

        // nginx terminates TLS and forwards plain http with X-Forwarded-Proto: https.
        $this->get('http://lmi.example.gov.ph/login', ['X-Forwarded-Proto' => 'https'])
            ->assertOk()
            ->assertHeader('Strict-Transport-Security');
    }

    public function test_localhost_is_not_redirected(): void
    {
        config(['security.force_https' => true]);

        $this->get('http://localhost/login')->assertOk();
    }

    public function test_security_headers_and_hsts(): void
    {
        $this->get('https://lmi.example.gov.ph/login')
            ->assertOk()
            ->assertHeader('Strict-Transport-Security')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    // ==================== 4. INPUT VALIDATION & SANITIZATION ====================

    public function test_html_sanitizer_removes_scripts_and_handlers_but_keeps_formatting(): void
    {
        $dirty = '<p class="ql-align-center" onclick="steal()">Hello <strong>world</strong></p>'
               . '<script>alert(1)</script>'
               . '<a href="javascript:alert(1)">bad</a>'
               . '<a href="https://dole.gov.ph" target="_blank">good</a>'
               . '<img src=x onerror="alert(1)">'
               . '<span style="color: red; background-image: url(javascript:x)">text</span>'
               . '<iframe src="https://evil.test"></iframe>';

        $clean = HtmlSanitizer::clean($dirty);

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('alert(1)', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('<iframe', $clean);
        $this->assertStringContainsString('<strong>world</strong>', $clean);
        $this->assertStringContainsString('class="ql-align-center"', $clean);
        $this->assertStringContainsString('href="https://dole.gov.ph"', $clean);
        $this->assertStringContainsString('rel="noopener noreferrer"', $clean);
        $this->assertStringContainsString('style="color: red"', $clean);
    }

    public function test_html_sanitizer_keeps_unicode_text(): void
    {
        $this->assertSame('<p>Pagsasanay – ₱20,000 ñ</p>', HtmlSanitizer::clean('<p>Pagsasanay – ₱20,000 ñ</p>'));
    }

    public function test_public_form_input_is_stripped_of_html(): void
    {
        Route::post('/__strip-test', fn (\Illuminate\Http\Request $r) => $r->all())->middleware(StripHtmlInput::class);

        $this->postJson('/__strip-test', [
            'company'  => 'ACME <script>alert(1)</script>',
            'skills'   => ['<img src=x onerror=alert(1)>Welding'],
            'duration' => '< 3 months',
        ])->assertExactJson([
            'company'  => 'ACME alert(1)',
            'skills'   => ['Welding'],
            'duration' => '< 3 months',
        ]);
    }

    public function test_uploaded_file_name_never_uses_the_client_extension(): void
    {
        // A real PNG with PHP code appended, uploaded as "shell.php". (UploadedFile::fake()
        // guesses the type from the name, so a real file is used to test content detection.)
        $path = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==') . '<?php echo 1; ?>');
        $file = new \Illuminate\Http\UploadedFile($path, 'shell.php', null, null, true);

        $this->assertStringEndsWith('.png', $file->hashName());
        $this->assertStringNotContainsString('.php', $file->hashName());
        @unlink($path);

        $controller = file_get_contents(app_path('Http/Controllers/Module4/ProgramAdminController.php'))
                    . file_get_contents(app_path('Http/Controllers/Module6/WeeklyCardSettingController.php'));
        $this->assertStringNotContainsString('getClientOriginalExtension', $controller);
    }

    public function test_internal_error_details_are_hidden_from_users(): void
    {
        config(['app.debug' => false]);

        $message = SafeError::message(new \RuntimeException('SQLSTATE[42S02]: table secret_table missing'));

        $this->assertStringNotContainsString('secret_table', $message);
        $this->assertStringContainsString('Ref:', $message);
    }

    public function test_unpublished_drafts_require_login(): void
    {
        $this->getJson('/api/supply-side-analysis/pending-all')->assertStatus(401);
        $this->getJson('/api/analysis-templates/pending-all')->assertStatus(401);
    }

    // ==================== 5. SECURITY LOGGING ====================

    public function test_failed_logins_and_staff_changes_are_logged(): void
    {
        $user = $this->makeUser();

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong']);
        $this->assertTrue($this->securityLogContains('auth.login_failed'));

        $this->actingAs($user)->put('/account/password', [
            'current_password'      => self::STRONG_PASSWORD,
            'password'              => 'An0ther!Strong#Pass',
            'password_confirmation' => 'An0ther!Strong#Pass',
        ]);

        $log = file_get_contents($this->securityLog);
        $this->assertStringContainsString('audit.change', $log);
        $this->assertStringContainsString('auth.password_changed', $log);
        $this->assertStringNotContainsString('An0ther!Strong#Pass', $log, 'Passwords must never be logged');
    }

    public function test_role_violations_are_logged(): void
    {
        $statistician = $this->makeUser('statistician');

        $this->actingAs($statistician)->get('/admin/JobMarketDemandsForm')->assertForbidden();
        $this->assertTrue($this->securityLogContains('access.forbidden'));
    }

    public function test_security_report_command_runs(): void
    {
        $this->artisan('security:report', ['--hours' => 1])->assertSuccessful();
    }

    // ==================== 6. PASSWORD POLICY ====================

    public function test_weak_passwords_are_rejected(): void
    {
        $user = $this->makeUser();

        foreach (['short1!A', 'alllowercase123!', 'NoNumbersHere!!', 'NoSymbols12345A'] as $weak) {
            $this->actingAs($user)->put('/account/password', [
                'current_password'      => self::STRONG_PASSWORD,
                'password'              => $weak,
                'password_confirmation' => $weak,
            ])->assertSessionHasErrors('password');
        }

        $this->assertTrue(Hash::check(self::STRONG_PASSWORD, $user->fresh()->password));
    }

    public function test_change_password_requires_current_password(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)->put('/account/password', [
            'current_password'      => 'not-my-password',
            'password'              => 'An0ther!Strong#Pass',
            'password_confirmation' => 'An0ther!Strong#Pass',
        ])->assertSessionHasErrors('current_password');
    }

    public function test_change_password_page_renders(): void
    {
        $this->actingAs($this->makeUser())->get('/account/password')->assertOk()->assertSee('Change Password');
    }

    public function test_forgot_and_reset_password_flow(): void
    {
        Notification::fake();
        $user = $this->makeUser();

        $this->get('/forgot-password')->assertOk();

        // Same response for unknown emails (no account enumeration).
        $this->post('/forgot-password', ['email' => 'nobody@example.test'])->assertSessionHas('success');
        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHas('success');

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function ($n) use (&$token) {
            $token = $n->token;
            return true;
        });

        $this->get('/reset-password/' . $token . '?email=' . urlencode($user->email))->assertOk();

        // Weak password refused.
        $this->post('/reset-password', [
            'token' => $token, 'email' => $user->email,
            'password' => 'weak', 'password_confirmation' => 'weak',
        ])->assertSessionHasErrors('password');

        $this->post('/reset-password', [
            'token' => $token, 'email' => $user->email,
            'password' => 'Brand!New#Passw0rd', 'password_confirmation' => 'Brand!New#Passw0rd',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('Brand!New#Passw0rd', $user->fresh()->password));
    }

    // ==================== 7. CSRF ====================

    public function test_requests_without_csrf_token_are_rejected_and_logged(): void
    {
        // The test framework disables CSRF checks by default; turn them back on.
        $this->app->instance('env', 'production');
        $user = $this->makeUser();

        $this->actingAs($user)->put('/account/password', [
            'current_password'      => self::STRONG_PASSWORD,
            'password'              => 'An0ther!Strong#Pass',
            'password_confirmation' => 'An0ther!Strong#Pass',
        ])->assertStatus(419);

        $this->assertTrue(Hash::check(self::STRONG_PASSWORD, $user->fresh()->password));
        $this->assertTrue($this->securityLogContains('request.csrf_mismatch'));
    }
}
