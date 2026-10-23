<?php

namespace App\Models\Sprs;

class ReferralAndPlacement extends SubmissionSection
{
    protected $table = 'referral_and_placement';

    protected $fillable = [
        'sprs_submission_id',

        // Private establishment
        'pvt_referred_total',
        'pvt_referred_local_total',
        'pvt_referred_local_female',
        'pvt_referred_overseas_total',
        'pvt_referred_overseas_female',
        'pvt_placed_total',
        'pvt_placed_local_total',
        'pvt_placed_local_female',
        'pvt_placed_overseas_total',
        'pvt_placed_overseas_female',

        // Government establishment
        'govt_referred_total',
        'govt_referred_local_total',
        'govt_referred_local_female',
        'govt_referred_overseas_total',
        'govt_referred_overseas_female',
        'govt_placed_total',
        'govt_placed_overseas_total_1',
        'govt_placed_overseas_female_1',
        'govt_placed_overseas_total_2',
        'govt_placed_overseas_female_2',
    ];

    /**
     * The four totals that should equal the sum of their parts. The
     * FormRequest enforces this at submit time; this map keeps the rule
     * in one place so the JS in step 6 can mirror it.
     *
     * Government "placed" is missing on purpose — its breakdown is the
     * unresolved _1 / _2 pair, so there is no reliable sum to check yet.
     *
     * @return array<string, array<int, string>>
     */
    public static function totalRules(): array
    {
        return [
            'pvt_referred_total' => [
                'pvt_referred_local_total',
                'pvt_referred_overseas_total',
            ],
            'pvt_placed_total' => [
                'pvt_placed_local_total',
                'pvt_placed_overseas_total',
            ],
            'govt_referred_total' => [
                'govt_referred_local_total',
                'govt_referred_overseas_total',
            ],
        ];
    }
}
