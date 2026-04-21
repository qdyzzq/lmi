<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplySideAnalysisSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            [
                'province'      => 'Davao City',
                'academic_year' => '2023-2024',
                'analysis_text' => "Davao City's enrollment for the 2023-2024 academic year is primarily driven by three core disciplines: Business Administration (19.9%), Medical and Allied (19.3%), and Education Science (14.6%). Together with Engineering and Technology (11.1%), these fields constitute nearly 65% of the total student population, reflecting a strong regional focus on professional services, healthcare, and technical industries.\n\nWhile disciplines like Criminal Justice (7.3%) and IT-Related fields (5.6%) maintain a solid secondary presence, the remaining enrollment is spread across more than 15 specialized programs. Many critical sectors, including Natural Science (1.2%) and Agriculture (0.4%), see significantly lower intake, each representing less than 2% of total enrollees. This distribution suggests a workforce supply heavily concentrated in traditional professional sectors, with a notable gap in enrollment for primary industries and basic sciences.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            [
                'province'      => 'Davao City',
                'academic_year' => '2022-2023',
                'analysis_text' => "The academic landscape in Davao City is currently led by a \"Big Three\" in terms of student volume: Medical and Allied (20.7%), Business Administration (19.8%), and Education Science (15.5%). These three pillars, alongside a healthy 10.4% share for Engineering and Technology, represent the vast majority of the city's higher education output.\n\nOutside of these primary sectors, student interest is more fragmented. Criminal Justice (7.9%) and IT-Related Disciplines (5.0%) serve as significant secondary markets. However, the data reveals a steep drop-off when moving into more specialized fields; Maritime (4.5%) and Social Sciences (4.4%) hold moderate positions, while nearly a dozen other programs, such as Natural Science (1.0%) and Agriculture (0.4%), struggle to capture even a 2% share of total enrollees.\n\nThe region's educational supply side is heavily optimized for the healthcare and service economies, while showing a distinct shortage of enrollees in the basic sciences and primary production sectors.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            [
                'province'      => 'Davao de Oro',
                'academic_year' => '2023-2024',
                'analysis_text' => "Enrollment in Davao de Oro for the 2023–2024 academic year shows a strong concentration in a few key sectors, with 79.3% of students enrolled in Business Administration (43.9%) and Education Science (35.4%). When combined with Criminal Justice Education (11.2%), these three disciplines represent over 90% of the province's total higher education population. This trend highlights a steady supply of future professionals for the region's administrative, teaching, and security sectors.\n\nThe current data also reflects the specialized nature of the province's academic offerings. While Agriculture, Forestry, and Fisheries maintains a presence at 6.7%, several fields such as Engineering, Medical and Allied, and Maritime report 0.0% enrollment. This is largely due to the limited availability of specialized programs in fields such as engineering and healthcare within Davao de Oro. While higher education institutions exist in the province, many of these specialized programs are more commonly offered in nearby academic hubs such as Davao City and Tagum City, prompting students to pursue their studies outside the province. This situation presents a clear opportunity for growth, as the introduction of technical and healthcare programs locally could help diversify and strengthen the province's future workforce.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            [
                'province'      => 'Davao de Oro',
                'academic_year' => '2022-2023',
                'analysis_text' => "Enrollment in Davao de Oro for the 2022–2023 academic year is characterized by a high concentration in traditional professional tracks, with 78.0% of the student population enrolled in either Business Administration (42.1%) or Education Science (35.9%). When including Criminal Justice Education (10.9%), these three disciplines represent nearly 90% of the province's total higher education community. This demonstrates a strong local commitment to developing a workforce for the administrative, academic, and public safety sectors.\n\nThe province also maintains a steady focus on its local industries, with Agriculture, Forestry, and Fisheries accounting for 8.5% of enrollees. However, several technical and healthcare-related disciplines, including Engineering and Technology, Medical and Allied, and Maritime, show 0.0% enrollment. This could be primarily due to the current absence of these specialized programs in the province's main institutions. Consequently, students seeking these degrees typically look to established academic centers in Davao City or Tagum City. This indicates a strategic opportunity for future institutional expansion to provide more diverse technical and medical training within the province.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            [
                'province'      => 'Davao del Norte',
                'academic_year' => '2023-2024',
                'analysis_text' => "For the 2023–2024 academic year, Davao del Norte's higher education landscape is defined by a significant preference for professional and administrative career paths. Business Administration (35.5%) stands as the clear leader in market share, which, when paired with Education Science (21.8%), accounts for over half of the province's total student body. This core is further bolstered by Criminal Justice Education (13.8%), creating a dominant trio of disciplines that represents 71.1% of all enrollees.\n\nBeyond these primary sectors, the province displays a healthy variety of secondary academic interests. Most notable is the presence of Mathematics (6.5%), which surprisingly holds a higher share than more common technical fields like IT-Related Disciplines (5.7%) and Engineering and Technology (3.1%). While Service Trades (5.7%) and Agriculture (3.6%) continue to contribute to the local workforce supply, specialized sectors such as Medical and Allied (0.3%) and Maritime (0.0%) remain minimal within the province. This distribution suggests that while Davao del Norte is a strong hub for business and teacher education, students seeking medical or heavy technical training likely look to external regional centers.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            [
                'province'      => 'Davao del Norte',
                'academic_year' => '2022-2023',
                'analysis_text' => "Enrollment in Davao del Norte for the 2022–2023 academic year is largely defined by a few key professional sectors, with Business Administration (35.0%) and Education Science (23.8%) accounting for nearly 59% of the student population. When combined with Criminal Justice Education (13.5%), these three disciplines represent 72.3% of the total higher education community in the province. This trend highlights a strong regional output of professionals aimed at the administrative, academic, and public safety workforces.\n\nThe remaining enrollment shows a more balanced distribution among technical and service-oriented fields compared to neighboring provinces. Service Trades (5.2%), Medical and Allied (5.2%), IT-Related Disciplines (4.9%), and Agriculture (4.0%) all maintain a steady presence. While technical fields like Engineering and Technology (4.0%) are represented, several specialized programs, including Maritime, Mathematics, and Architecture, report 0.0% enrollment, indicating that students pursuing these specific paths likely seek institutions in nearby urban hubs like Tagum City or Davao City. Overall, the data shows a province with a solid professional core and an established secondary foundation in essential service and technology sectors.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            [
                'province'      => 'Davao del Sur',
                'academic_year' => '2023-2024',
                'analysis_text' => "Enrollment in Davao del Sur for the 2023–2024 academic year remains led by Education Science (34.1%) and Business Administration (20.1%), which together account for over half of the province's student population. When joined by Criminal Justice Education (14.9%), these three disciplines represent 69.1% of all enrollees, indicating a sustained regional focus on the educational, administrative, and public safety sectors.\n\nThe province shows a diversified secondary tier of academic interest, with IT-Related Disciplines (6.9%), Agriculture (5.9%), and Medical and Allied (5.7%) fields maintaining a balanced presence. While technical fields like Engineering and Technology (3.6%) continue to contribute to the local talent pool, highly specialized sectors such as Architecture, Home Economics, and Maritime report 0.0% enrollment. This distribution suggests that while Davao del Sur is a robust center for teacher training and business, students pursuing niche technical or maritime careers likely continue to utilize specialized institutions in nearby urban hubs.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            [
                'province'      => 'Davao del Sur',
                'academic_year' => '2022-2023',
                'analysis_text' => "Higher education enrollment in Davao del Sur for the 2022–2023 academic year is characterized by a significant focus on service and administrative disciplines. Education Science stands as the most popular choice by a wide margin, capturing 43.9% of the student population. This is followed by Business Administration (19.1%), which together with education, accounts for 63% of all enrollees in the province.\n\nThe province also shows a strong commitment to its regional industries, with Agriculture, Forestry, and Fisheries holding a notable 10.6% market share. Criminal Justice Education (10.2%) maintains a solid secondary presence, rounding out the top four disciplines that collectively represent over 83% of the student body.\n\nTechnical and specialized fields such as IT-Related Disciplines (4.8%) and Engineering and Technology (3.5%) show a more focused intake, while healthcare representation remains relatively low with Medical and Allied fields at 1.3%. Several niche programs, including Maritime, Architecture, and Mathematics, report 0.0% enrollment locally, suggesting that students in these specialized tracks likely utilize neighboring academic hubs like Davao City. This data reflects a province with a very strong foundation in teacher training and agricultural studies, supported by an emerging interest in technology and social sciences.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            [
                'province'      => 'Davao Occidental',
                'academic_year' => '2023-2024',
                'analysis_text' => "Enrollment in Davao Occidental for the 2023–2024 academic year remains centered on administrative and educational disciplines. Business Administration (32.9%) continues to be the most popular choice, followed by Education Science (22.6%), which together account for over 55% of the province's student population. This highlights a consistent local output of professionals destined for the corporate, public, and academic sectors.\n\nThe academic landscape shows a notable shift in secondary interests, with a significant 15.4% of enrollees categorized under Other Disciplines, making it the third-largest group in the province. Other active sectors include: Agriculture, Forestry, and Fisheries (7.1%), reflecting the province's primary industries; Natural Science (5.9%) and Criminal Justice Education (5.3%), which maintain steady representation; Mass Communication (4.8%) and IT-Related Disciplines (4.6%), showing a modest but healthy interest in media and technology.\n\nWhile enrollment in Engineering and Technology remains small at 1.5%, several specialized fields, including Medical and Allied, Maritime, and Architecture, report 0.0% enrollment. This lack of local participation in healthcare and technical fields is likely due to the limited availability of these specialized programs in the province, with students opting for larger academic centers in the region for these degrees.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            [
                'province'      => 'Davao Occidental',
                'academic_year' => '2022-2023',
                'analysis_text' => "Higher education in Davao Occidental for the 2022–2023 academic year is primarily anchored by a strong interest in business and social service sectors. Business Administration leads as the most popular discipline at 32.7%, followed closely by Education Science at 27.7%. Together with Criminal Justice Education (14.3%), these three fields account for 74.7% of the province's student population, highlighting a significant focus on administrative, academic, and public safety career paths.\n\nThe remaining student base is distributed across a mix of specialized and foundational programs. Medical and Allied (5.0%), Agriculture (4.9%), and IT-Related Disciplines (3.0%) maintain a steady presence, while Natural Science (2.8%) and Mass Communication (1.9%) round out the active academic interests. However, enrollment in heavy technical and specialized fields like Engineering and Technology (0.7%), Maritime (0.0%), and Architecture (0.0%) is minimal to non-existent within the province. This distribution suggests that while the province is effectively building its professional service workforce, students seeking high-level technical or medical training likely continue to look toward larger regional hubs.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            [
                'province'      => 'Davao Oriental',
                'academic_year' => '2023-2024',
                'analysis_text' => "Enrollment in Davao Oriental for the 2023–2024 academic year continues to be dominated by two primary disciplines, with Business Administration (41.9%) and Education Science (22.5%) accounting for nearly 65% of the student population. When combined with Criminal Justice Education (11.2%), these three fields represent over 75% of the province's total higher education community. This distribution indicates a steady workforce supply heavily concentrated in the administrative, academic, and public safety sectors.\n\nThe remaining student base is spread across various technical and professional disciplines, though at significantly lower volumes. IT-Related Disciplines (5.3%), Medical and Allied (4.8%), and Agriculture (4.4%) maintain a stable secondary presence, while Engineering and Technology holds a 3.3% share.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            [
                'province'      => 'Davao Oriental',
                'academic_year' => '2022-2023',
                'analysis_text' => "Higher education enrollment in Davao Oriental for the 2022–2023 academic year is characterized by a significant concentration in administrative and educational tracks. Business Administration leads as the most popular discipline, capturing 42.8% of the student population, followed by Education Science at 23.1%. Together, these two sectors represent nearly 66% of all enrollees, indicating a robust supply of future professionals for the region's corporate and academic workforces.\n\nThe province maintains a diverse secondary tier of academic interest, with Criminal Justice Education (9.7%) serving as the third-largest discipline. Engagement in primary industries and technology is also evident, as Agriculture, Forestry, and Fisheries (5.4%), IT-Related Disciplines (5.1%), and Engineering and Technology (3.6%) maintain a steady presence. While specialized fields like Mathematics (2.0%), Medical and Allied (2.0%), and Natural Science (1.9%) show active participation, several niche sectors, including Maritime, Architecture, and Home Economics, currently report 0.0% enrollment. This distribution suggests that while the province effectively meets the demand for professional and service roles, students seeking highly specialized technical or maritime training likely utilize broader regional hubs.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            [
                'province'      => 'Davao Region',
                'academic_year' => '2022-2023',
                'analysis_text' => "Higher education enrollment across Davao Region for the 2022–2023 academic year is characterized by a high concentration in professional and service-oriented sectors. Business Administration (26.9%) and Education Science (20.4%) stand as the primary drivers of student intake, collectively accounting for nearly half of the region's total enrollees. This core is further bolstered by a strong interest in Medical and Allied (11.9%) and Criminal Justice Education (10.4%) fields, bringing the combined share of these top four disciplines to 69.6%.\n\nThe remaining student population is distributed among a variety of technical and specialized programs, though at significantly lower volumes. Engineering and Technology (7.7%) and IT-Related Disciplines (5.0%) maintain a stable secondary presence, while disciplines such as Social and Behavioral Sciences (2.9%), Agriculture (2.5%), and Maritime (2.5%) show more focused participation. Despite the region's diverse economic needs, several specialized programs, including Natural Science (1.2%), Law (0.7%), and Mathematics (0.3%), represent minimal segments of the total enrollment. This distribution highlights a regional workforce supply heavily prioritized toward business, healthcare, and education, with opportunity for increased engagement in the foundational sciences and technical trades.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            [
                'province'      => 'Davao Region',
                'academic_year' => '2023-2024',
                'analysis_text' => "For the 2023–2024 academic year, higher education across Davao Region remains anchored by a strong preference for professional service sectors. Business Administration (28.1%) maintains its position as the leading discipline, followed by Education Science at 18.2%. Together, these two fields account for nearly half of the regional student population, indicating a consistent supply of future professionals for the corporate and academic workforces.\n\nThe regional academic landscape is further strengthened by significant interest in healthcare and public safety, with Medical and Allied fields holding an 11.5% share and Criminal Justice Education following closely at 10.9%. Technical disciplines also contribute notably to the regional profile, as Engineering and Technology (7.5%) and IT-Related Disciplines (5.9%) continue to attract a steady portion of enrollees.\n\nWhile the region demonstrates a broad range of study areas, enrollment in several specialized and foundational fields remains relatively small. Service Trades (3.2%), Maritime (2.7%), and Agriculture (2.3%) represent vital but smaller segments of the student body, while foundational sciences like Natural Science (1.3%) and Mathematics (0.3%) show minimal participation. This distribution suggests that while Davao Region is a robust center for business, healthcare, and education, there is an ongoing opportunity to expand engagement in technical, agricultural, and basic science programs to further diversify the regional economy.",
                'is_active'     => 1,
                'status'        => 'published',
            ],
            
        ];

        DB::table('supply_side_analysis')->insert($records);
    }
}