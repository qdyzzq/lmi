<?php

namespace App\Models\Sprs;

class DoleProgram extends SubmissionSection
{
    protected $table = 'dole_programs';

    protected $fillable = [
        'sprs_submission_id',
        'spes_regular_total',
        'spes_regular_female',
        'spes_lgu_implemented_total',
        'spes_lgu_implemented_female',
        'lra_sra_conducted',
        'job_fairs_tnk_conducted',
        'jobstart_dole_funded_total',
        'jobstart_dole_funded_female',
        'jobstart_lgu_funded_total',
        'jobstart_lgu_funded_female',
        'gip_beneficiaries_total',
        'gip_beneficiaries_female',
        'tupad_beneficiaries_total',
        'tupad_beneficiaries_female',
        'tulay_beneficiaries_total',
        'tulay_beneficiaries_female',
        'livelihood_beneficiaries_total',
        'livelihood_beneficiaries_female',
        'microbiz_beneficiaries_total',
        'microbiz_beneficiaries_female',
        'cbt_trainings_provided',
        'owwa_assisted_total',
        'owwa_assisted_female',
        'ofw_reintegration_assisted_total',
        'ofw_reintegration_assisted_female',
        'air_campaign_advocacies',
        'anti_child_labor_advocacies',
        'kasambahay_law_advocacies',
        'ojt_wap_assisted_total',
        'ojt_wap_assisted_female',
        'whip_placed_total',
        'whip_placed_female',
        'rwa_associations_registered',
        'labor_complaints_referred_total',
        'labor_complaints_referred_female',
        'green_dole_advocacies',
    ];

    /**
     * Sub-program groupings, for rendering the tab without hard-coding
     * 35 inputs in Blade. Key is the heading, value is the columns.
     *
     * @return array<string, array<int, string>>
     */
    public static function groups(): array
    {
        return [
            'SPES'                    => ['spes_regular_total', 'spes_regular_female', 'spes_lgu_implemented_total', 'spes_lgu_implemented_female'],
            'LRA / SRA'               => ['lra_sra_conducted'],
            'Job fairs / TNK'         => ['job_fairs_tnk_conducted'],
            'JobStart Philippines'    => ['jobstart_dole_funded_total', 'jobstart_dole_funded_female', 'jobstart_lgu_funded_total', 'jobstart_lgu_funded_female'],
            'GIP facilitation'        => ['gip_beneficiaries_total', 'gip_beneficiaries_female'],
            'TUPAD'                   => ['tupad_beneficiaries_total', 'tupad_beneficiaries_female'],
            'TULAY'                   => ['tulay_beneficiaries_total', 'tulay_beneficiaries_female'],
            'Livelihood assistance'   => ['livelihood_beneficiaries_total', 'livelihood_beneficiaries_female'],
            'MicroBiz incubation'     => ['microbiz_beneficiaries_total', 'microbiz_beneficiaries_female'],
            'Community skills (CBT)'  => ['cbt_trainings_provided'],
            'OWWA help desk'          => ['owwa_assisted_total', 'owwa_assisted_female'],
            'OFW reintegration'       => ['ofw_reintegration_assisted_total', 'ofw_reintegration_assisted_female'],
            'AIR campaign'            => ['air_campaign_advocacies'],
            'Anti-child labor'        => ['anti_child_labor_advocacies'],
            'Kasambahay law'          => ['kasambahay_law_advocacies'],
            'OJT / WAP'               => ['ojt_wap_assisted_total', 'ojt_wap_assisted_female'],
            'WHIP'                    => ['whip_placed_total', 'whip_placed_female'],
            'RWA registration'        => ['rwa_associations_registered'],
            'Labor complaints'        => ['labor_complaints_referred_total', 'labor_complaints_referred_female'],
            'Green our DOLE'          => ['green_dole_advocacies'],
        ];
    }
}
