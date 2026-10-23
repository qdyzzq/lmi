<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP</title>
</head>
<body class="m-0 p-0 bg-slate-100 font-sans" style="margin:0; padding:0; background:#f1f5f9; font-family:'Segoe UI', Arial, sans-serif;">

    <div class="max-w-[520px] mx-auto my-10 bg-white rounded-2xl overflow-hidden shadow-lg"
         style="max-width:520px; margin:40px auto; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08);">

        <!-- Header -->
        <div class="bg-gradient-to-br from-dole-blue to-dole-red px-10 py-9 text-center"
             style="background:linear-gradient(135deg, #003087 0%, #CE1126 100%); padding:36px 40px; text-align:center;">
            <h1 class="m-0 text-white text-lg font-bold uppercase tracking-wide"
                style="margin:0; color:#fff; font-size:18px; font-weight:700; letter-spacing:0.05em; text-transform:uppercase;">
                Department of Labor and Employment
            </h1>
            <p class="mt-1 text-white/75 text-[13px]" style="margin:4px 0 0; color:rgba(255,255,255,0.75); font-size:13px;">
                Region XI &ndash; SPRS
            </p>
        </div>

        <!-- Body -->
        <div class="px-10 py-10 text-center" style="padding:40px; text-align:center;">
            <p class="text-slate-600 text-[15px] mb-6 leading-relaxed"
               style="color:#475569; font-size:15px; margin:0 0 24px; line-height:1.6;">
                Use the OTP below to complete your login. This code expires in <strong>10 minutes</strong>.
            </p>

            <div class="inline-block bg-slate-50 border-2 border-dashed border-dole-blue rounded-xl px-10 py-4.5 mb-6"
                 style="display:inline-block; background:#f8fafc; border:2px dashed #003087; border-radius:12px; padding:18px 40px; margin-bottom:24px;">
                <div class="text-[42px] font-extrabold tracking-[0.25em] text-dole-blue"
                     style="font-size:42px; font-weight:800; letter-spacing:0.25em; color:#003087;">
                    {{ $otp }}
                </div>
            </div>

            <br>

            <span class="inline-block bg-yellow-100 text-yellow-800 text-[13px] font-semibold px-3.5 py-1.5 rounded-full mb-6"
                  style="display:inline-block; background:#fef9c3; color:#854d0e; font-size:13px; font-weight:600; padding:6px 14px; border-radius:20px; margin-bottom:24px;">
                ⏱ Valid for 10 minutes
            </span>

            <p class="text-slate-400 text-[13px] m-0" style="color:#94a3b8; font-size:13px; margin:0;">
                Do not share this code with anyone.<br>
                If you did not request this, please contact support immediately.
            </p>
        </div>

        <!-- Footer -->
        <div class="bg-slate-50 px-10 py-5 text-center border-t border-slate-200"
             style="background:#f8fafc; padding:20px 40px; text-align:center; border-top:1px solid #e2e8f0;">
            <p class="m-0 text-slate-400 text-xs" style="margin:0; color:#94a3b8; font-size:12px;">
                © {{ date('Y') }} DOLE Region XI &mdash; This is an automated message, please do not reply.
            </p>
        </div>

    </div>
</body>
</html>