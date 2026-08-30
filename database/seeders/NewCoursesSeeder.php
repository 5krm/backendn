<?php

namespace Database\Seeders;

use App\Enums\CourseStatus;
use App\Enums\Level;
use App\Models\Category;
use App\Models\Courses\Course;
use App\Models\Courses\CoursePrice;
use App\Models\Courses\CourseSection;
use App\Models\Lessons\Lesson;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewCoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->updateTutorProfiles();

        $coursesData = [
            // ==================== ARABIC COURSES (3) ====================
            [
                'tutor_email' => 'tutor2@ngoacademy.test', // Prof. Ahmed Hassan (user_id: 767)
                'category_slug' => 'project-management',
                'title' => 'إدارة دورة حياة المشاريع التنموية والإنسانية (PCM)',
                'description' => 'دورة تدريبية متقدمة وشاملة موجهة للعاملين في المنظمات غير الحكومية، تغطي جميع مراحل إدارة دورة حياة المشروع بدءاً من تقييم الاحتياجات وتصميم مصفوفة الإطار المنطقي، مروراً بإعداد الخطط التشغيلية والموازنات المالية، وصولاً إلى الرصد وإجراءات الإغلاق السليم.',
                'objectives' => '<ul><li>إتقان أدوات تقييم الاحتياجات الميدانية وتحليل أصحاب المصلحة.</li><li>صياغة مصفوفة الإطار المنطقي ومؤشرات الأداء الذكية باحترافية.</li><li>إعداد الخطط الزمنية وهيكل تقسيم العمل وموازنات المشاريع الدقيقة.</li><li>إدارة المخاطر التشغيلية وتطبيق معايير الإغلاق السليم للمشاريع.</li></ul>',
                'lang' => 'ar',
                'level' => Level::Intermediate,
                'is_free' => false,
                'price' => 140.00,
                'old_price' => 220.00,
                'order' => 12,
                'sections' => [
                    [
                        'title' => 'الوحدة الأولى: مدخل وأساسيات إدارة دورة حياة المشروع',
                        'lessons' => [
                            [
                                'title' => 'مفهوم إدارة المشاريع التنموية ومعايير PMD Pro',
                                'content' => "تعتبر إدارة دورة حياة المشروع (Project Cycle Management - PCM) الركيزة الأساسية لنجاح المشاريع الإنسانية والتنموية.\n\nفي هذا الدرس سنتعرف على مراحل دورة المشروع الست وفق المعايير الدولية:\n1. مرحلة تحديد الاحتياجات (Identification)\n2. مرحلة التصميم والتخطيط (Design & Planning)\n3. مرحلة التمويل والتعاقد (Funding)\n4. مرحلة التنفيذ والإشراف (Implementation)\n5. مرحلة الرصد والتقييم (Monitoring & Evaluation)\n6. مرحلة الإغلاق والتقييم النهائي (Closure & Evaluation)",
                                'duration' => 20,
                            ],
                            [
                                'title' => 'أدوات تقييم الاحتياجات وتحليل شجرة المشكلات',
                                'content' => "يعد تقييم الاحتياجات الميدانية بدقة الخطوة الأهم لتفادي تعثر المشاريع.\n\nأدوات تحليل المشكلات:\n- شجرة المشكلات (Problem Tree Analysis) للربط بين الأسباب الجذرية والتأثيرات.\n- شجرة الأهداف (Objective Tree) لتحويل المشكلات إلى حلول ومخرجات قابلة للتحقيق.\n- مصفوفة ترتيب الأولويات الميدانية والمجتمعية.",
                                'duration' => 25,
                            ],
                            [
                                'title' => 'تحليل أصحاب المصلحة وتحديد الفئات المستهدفة',
                                'content' => "كيف تحدد المستفيدين المباشرين وغير المباشرين؟\n\n- مصفوفة التأثير والاهتمام (Power-Interest Matrix)\n- آليات إشراك المجتمع المحلي في صنع القرار\n- ضمان شمولية الفئات المستضعفة وذوي الإعاقة.",
                                'duration' => 20,
                            ],
                        ],
                    ],
                    [
                        'title' => 'الوحدة الثانية: مرحلة التصميم ومصفوفة الإطار المنطقي (LogFrame)',
                        'lessons' => [
                            [
                                'title' => 'بناء وتفكيك مصفوفة الإطار المنطقي',
                                'content' => "مصفوفة الإطار المنطقي هي الأداة المعتمدة لدى كبرى الجهات المانحة الدولية.\n\nهيكل المصفوفة الأساسي:\n- الهدف العام / الأثر طويل المدى (Impact / Overall Objective)\n- النتائج المباشرة للمشروع (Outcomes / Specific Objectives)\n- المخرجات الملموسة (Outputs)\n- الأنشطة التنفيذية (Activities)",
                                'duration' => 30,
                            ],
                            [
                                'title' => 'تحديد المؤشرات الذكية ووسائل التحقق',
                                'content' => "صياغة مؤشرات أداء ذكية (SMART):\n- محددة وقابلة للقياس وقابلة للتحقيق وذات صلة ومحددة زمنياً.\n- تحديد وسائل التحقق (Means of Verification) ومصادر البيانات.\n- بناء خط الأساس (Baseline) والنتائج المستهدفة (Targets).",
                                'duration' => 25,
                            ],
                            [
                                'title' => 'تحليل الافتراضات وإدارة المخاطر التشغيلية',
                                'content' => "كل مشروع يواجه تحديات بيئية وأمنية واقتصادية.\n\n- صياغة الافتراضات الإيجابية في الإطار المنطقي\n- إعداد سجل المخاطر (Risk Register)\n- خطط التخفيف من آثار المخاطر (Risk Mitigation Strategies).",
                                'duration' => 20,
                            ],
                        ],
                    ],
                    [
                        'title' => 'الوحدة الثالثة: التخطيط التنفيذي وإدارة الموازنات',
                        'lessons' => [
                            [
                                'title' => 'إعداد هيكل تقسيم العمل (WBS) والجدول الزمني (Gantt Chart)',
                                'content' => "تفكيك الأنشطة إلى حزم عمل قابلة للإدارة وتحديد التبعيات والمسار الحرج (Critical Path) باستخدام مخططات غانت ومصفوفة المسؤوليات RACI.",
                                'duration' => 25,
                            ],
                            [
                                'title' => 'بناء الموازنة المالية التفصيلية وتوزيع التكاليف',
                                'content' => "كيفية إعداد موازنة متوافقة مع متطلبات المانحين تشمل التكاليف المباشرة والتشغيلية والتكاليف المشتركة وتتبع معدل الحرق المالي (Burn Rate).",
                                'duration' => 30,
                            ],
                            [
                                'title' => 'إجراءات الإغلاق السليم وتوثيق الدروس المستفادة',
                                'content' => "الإغلاق الإداري والمالي والتعاقدي للمشروع، وإعداد التقرير الختامي، وتسليم الأصول وتوثيق أفضل الممارسات وقصص النجاح.",
                                'duration' => 20,
                            ],
                        ],
                    ],
                ],
            ],

            [
                'tutor_email' => 'tutor7@ngoacademy.test', // Dr. Fatima Al-Sayed (user_id: 772)
                'category_slug' => 'gender-protection',
                'title' => 'حماية وصون الطفل والوقاية من الاستغلال في العمل الإنساني',
                'description' => 'مساق تدريبي أساسي للكوادر الميدانية والإغاثية، يهدف إلى ترسيخ مبادئ صون الطفل (Child Safeguarding)، وتطبيق المعايير الدنيا لحماية الطفل (CPMS)، وآليات الوقاية من الاستغلال والاعتداء والتحرش الجنسي (PSEA)، وإنشاء قنوات إبلاغ آمنة وموثوقة.',
                'objectives' => '<ul><li>استيعاب المعايير الدنيا لحماية الطفل في العمل الإنساني (CPMS).</li><li>تطبيق سياسات صون الطفل ومدونات السلوك الأخلاقي في المنظمات.</li><li>فهم آليات منع الاستغلال والانتهاك الجنسي (PSEA) وتفعيل مسارات الإحالة.</li><li>تصميم مساحات وأنشطة آمنة وصديقة للأطفال في المخيمات والمجتمعات المضيفة.</li></ul>',
                'lang' => 'ar',
                'level' => Level::Beginner,
                'is_free' => true,
                'price' => 0.00,
                'old_price' => null,
                'order' => 13,
                'sections' => [
                    [
                        'title' => 'الوحدة الأولى: المبادئ التوجيهية لحماية الطفل',
                        'lessons' => [
                            [
                                'title' => 'مقدمة في معايير حماية الطفل الدولية (CPMS)',
                                'content' => "التعرف على المبادئ الأربعة الأساسية لمعايير حماية الطفل في حالات الطوارئ وفق دليل المعايير الدنيا للشبكة العالمية لحماية الطفل.",
                                'duration' => 20,
                            ],
                            [
                                'title' => 'تحديد وتصنيف مخاطر الحماية التي تواجه الأطفال',
                                'content' => "تحليل مخاطر العنف، الإهمال، عمالة الأطفال، الزواج المبكر، والتجنيد الإجباري في أوقات النزاعات والنزوح وتحديد استراتيجيات الوقاية.",
                                'duration' => 25,
                            ],
                        ],
                    ],
                    [
                        'title' => 'الوحدة الثانية: سياسات الصون وآليات الوقاية من الاستغلال (PSEA)',
                        'lessons' => [
                            [
                                'title' => 'سياسة صون الطفل المؤسسية ومدونة السلوك',
                                'content' => "كيفية صياغة وتطبيق سياسة الصون على موظفي وموردي ومتطوعي المنظمة لمنع أي أذى قد يلحق بالأطفال وتطبيق فحص الخلفية الجنائية.",
                                'duration' => 25,
                            ],
                            [
                                'title' => 'الوقاية من الاستغلال والاعتداء الجنسي (PSEA)',
                                'content' => "معايير اللجنة الدائمة المشتركة بين الوكالات (IASC) والتزام عدم التسامح مطلقاً مع الاستغلال والاعتداء الجنسي وإساءة استخدام السلطة.",
                                'duration' => 30,
                            ],
                            [
                                'title' => 'قنوات الشكاوى والملاحظات الآمنة وسرية المعلومات',
                                'content' => "تأسيس قنوات إبلاغ سرية وسهلة الوصول للأطفال والمجتمع المحلي، وإدارة البلاغات بحيادية وموثوقية وسرية تامة.",
                                'duration' => 20,
                            ],
                        ],
                    ],
                    [
                        'title' => 'الوحدة الثالثة: إدارة الحالات ومسارات الإحالة الآمنة',
                        'lessons' => [
                            [
                                'title' => 'مبادئ إدارة حالات حماية الطفل (Case Management)',
                                'content' => "الخطوات الست لإدارة الحالات: الاستقبال والفرز، التقييم الشامل، خطة التدخل، التنفيذ، المتابعة الدورية، وإغلاق الحالة بنجاح.",
                                'duration' => 25,
                            ],
                            [
                                'title' => 'بناء خريطة الخدمات ومسار الإحالة الآمن',
                                'content' => "التنسيق مع الشركاء الإنسانيين لإنشاء مسار إحالة متعدد القطاعات (صحي، نفسي، تعليمي، قانوني) وضمان حماية البيانات وحفظ كرامة المستفيدين.",
                                'duration' => 20,
                            ],
                        ],
                    ],
                ],
            ],

            [
                'tutor_email' => 'tutor4@ngoacademy.test', // Dr. Omar Al-Rashid (user_id: 769)
                'category_slug' => 'monitoring-evaluation',
                'title' => 'منظومة المتابعة والتقييم والمساءلة والتعلم (MEAL)',
                'description' => 'دورة عملية متخصصة في بناء وتطوير منظومة MEAL فعالة داخل المنظمات التنموية والإنسانية، تغطي تصميم خطط الرصد، وأدوات جمع البيانات الرقمية الميدانية عبر KoboToolbox، وتقنيات التحليل الإحصائي، وإجراءات المساءلة المجتمعية وإدارة المعرفة.',
                'objectives' => '<ul><li>تصميم مصفوفة خطة المتابعة والتقييم الشاملة (MEAL Plan).</li><li>بناء استمارات جمع البيانات الميدانية الرقمية باستخدام KoboToolbox وODK.</li><li>تطبيق منهجيات التحليل الكمي والنوعي للبيانات وقياس الأثر المجتمعي.</li><li>تفعيل آليات المساءلة المجتمعية (Accountability) وجلسات التعلم المؤسسي.</li></ul>',
                'lang' => 'ar',
                'level' => Level::Intermediate,
                'is_free' => false,
                'price' => 165.00,
                'old_price' => 250.00,
                'order' => 14,
                'sections' => [
                    [
                        'title' => 'الوحدة الأولى: الإطار المفاهيمي لمنظومة MEAL',
                        'lessons' => [
                            [
                                'title' => 'الركائز الأربع لمنظومة MEAL ودورها في إدارة المشاريع',
                                'content' => "الفرق الجوهري بين المتابعة (Monitoring)، التقييم (Evaluation)، المساءلة (Accountability)، والتعلم (Learning)، وتكاملها في تعزيز كفاءة المنظمات وشفافيتها.",
                                'duration' => 20,
                            ],
                            [
                                'title' => 'نظرية التغيير (Theory of Change) ونماذج النتائج',
                                'content' => "بناء سلسلة النتائج المنطقية وتحديد الفرضيات المسبقة ومسارات التحول لربط أنشطة المشروع بالأثر التنموي المستدام.",
                                'duration' => 25,
                            ],
                            [
                                'title' => 'تصميم جدول تتبع المؤشرات (ITT - Indicator Tracking Table)',
                                'content' => "تحديد قيم خط الأساس (Baseline)، المستهدفات الدورية، وتعيين المسؤوليات وتكرار جمع البيانات ووسائل التحقق.",
                                'duration' => 30,
                            ],
                        ],
                    ],
                    [
                        'title' => 'الوحدة الثانية: أدوات جمع البيانات الرقمية وضبط الجودة',
                        'lessons' => [
                            [
                                'title' => 'تصميم الاستبيانات الميدانية عبر KoboToolbox / ODK',
                                'content' => "تطوير الاستمارات الرقمية، استخدام منطق التخطي (Skip Logic)، تحديد القيود والشروط، والجمع دون اتصال بالإنترنت وإدارة صلاحيات جامعي البيانات.",
                                'duration' => 35,
                            ],
                            [
                                'title' => 'أساليب أخذ العينات وضمان جودة البيانات (DQA)',
                                'content' => "اختيار العينات الإحصائية الممثلة، وتطبيق فحوصات جودة البيانات الميدانية (Data Quality Assessment) وفق المعايير الخمسة للجودة.",
                                'duration' => 25,
                            ],
                            [
                                'title' => 'أدوات البحث النوعي ومجموعات النقاش المركزة (FGDs)',
                                'content' => "إدارة جلسات النقاش البؤرية، المقابلات المعمقة مع أصحاب المصلحة، والترميز النوعي للبيانات وتحليل السياق المجتمعي.",
                                'duration' => 25,
                            ],
                        ],
                    ],
                    [
                        'title' => 'الوحدة الثالثة: تحليل البيانات والمساءلة والتعلم المستمر',
                        'lessons' => [
                            [
                                'title' => 'تحليل البيانات وإعداد التقارير التفاعلية والداشبورد',
                                'content' => "استخدام Power BI وExcel لتحويل البيانات إلى لوحات تحكم مرئية تفاعلية تساعد صناع القرار وفريق المشروع على اتخاذ قرارات مبنية على الأدلة.",
                                'duration' => 30,
                            ],
                            [
                                'title' => 'معايير المساءلة الإنسانية الأساسية (CHS) والتعلم المؤسسي',
                                'content' => "تطبيق المعيار الإنساني الأساسي للجودة والمساءلة (CHS)، وإدارة التغذية الراجعة، وعقد ورش عمل استخلاص الدروس (Lessons Learned Workshops).",
                                'duration' => 25,
                            ],
                        ],
                    ],
                ],
            ],

            // ==================== ENGLISH COURSES (3) ====================
            [
                'tutor_email' => 'tutor1@ngoacademy.test', // Dr. Sarah Mitchell (user_id: 766)
                'category_slug' => 'leadership-governance',
                'title' => 'Strategic Leadership and Governance in Non-Profit Organizations',
                'description' => 'An executive-level masterclass designed for NGO executives, board members, and senior program managers. Explore strategic visioning, board oversight, legal compliance, risk tolerance, agile leadership, and organizational resilience in challenging humanitarian and development landscapes.',
                'objectives' => '<ul><li>Formulate resilient organizational strategies responsive to dynamic global funding trends.</li><li>Establish robust board governance, fiduciary duty, and compliance structures.</li><li>Foster an inclusive, ethical culture and champion high-performing multidisciplinary teams.</li><li>Navigate organizational transformation, crisis management, and sustainable scaling.</li></ul>',
                'lang' => 'en',
                'level' => Level::Advanced,
                'is_free' => false,
                'price' => 195.00,
                'old_price' => 310.00,
                'order' => 15,
                'sections' => [
                    [
                        'title' => 'Module 1: Foundations of Non-Profit Governance & Board Dynamics',
                        'lessons' => [
                            [
                                'title' => 'Roles, Responsibilities & Fiduciary Duties of NGO Boards',
                                'content' => "Understanding the vital balance between governance oversight and operational management. Core topics:\n- Board charters and committee structures\n- Duty of care, loyalty, and obedience\n- Mitigating conflicts of interest and ensuring regulatory compliance.",
                                'duration' => 25,
                            ],
                            [
                                'title' => 'Executive Leadership and Strategic Board Relations',
                                'content' => "Building effective partnerships between the CEO/Executive Director and the Board of Directors. Strategies for transparent reporting, alignment, and collaborative decision-making.",
                                'duration' => 20,
                            ],
                            [
                                'title' => 'Institutional Transparency, Ethics and Anti-Corruption Policies',
                                'content' => "Establishing integrity frameworks, whistleblower protections, code of ethics, and conflict-of-interest registers across all organizational tiers.",
                                'duration' => 25,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Module 2: Strategic Thinking and Organizational Agility',
                        'lessons' => [
                            [
                                'title' => 'Strategic Environmental Scanning & Scenario Planning',
                                'content' => "Utilizing SWOT, PESTLE, and stakeholder mapping to anticipate geopolitical, socio-economic, and donor funding shifts.",
                                'duration' => 30,
                            ],
                            [
                                'title' => 'Translating Strategic Vision into Measurable Key Results (OKRs)',
                                'content' => "Aligning organizational vision with operational programs through Objectives and Key Results (OKRs) and balanced scorecards.",
                                'duration' => 25,
                            ],
                            [
                                'title' => 'Leading Cultural Transformation and Organizational Change',
                                'content' => "Managing change fatigue, cultivating psychological safety, and driving continuous learning within humanitarian organizations.",
                                'duration' => 25,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Module 3: Crisis Leadership & Financial Sustainability',
                        'lessons' => [
                            [
                                'title' => 'Crisis Preparedness and Business Continuity Planning',
                                'content' => "Leading with resilience during acute emergencies, reputational crises, and rapid security deterioration.",
                                'duration' => 30,
                            ],
                            [
                                'title' => 'Financial Sustainability and Diversified Revenue Strategies',
                                'content' => "Balancing restricted donor grants with unrestricted funding, institutional reserves, social enterprise models, and ethical private sector engagement.",
                                'duration' => 25,
                            ],
                        ],
                    ],
                ],
            ],

            [
                'tutor_email' => 'tutor6@ngoacademy.test', // Dr. James Wilson (user_id: 771)
                'category_slug' => 'humanitarian-aid',
                'title' => 'Emergency Logistics and Humanitarian Supply Chain Management',
                'description' => 'A comprehensive, operational course on orchestrating emergency supply chains, rapid procurement, warehouse operations, fleet management, and end-to-end aid distribution during sudden-onset disasters and complex humanitarian crises.',
                'objectives' => '<ul><li>Master the end-to-end humanitarian supply chain lifecycle from planning to last-mile delivery.</li><li>Execute compliant, cost-effective emergency procurement protocols under donor regulations.</li><li>Manage humanitarian storage facilities, inventory control, and cold chains.</li><li>Coordinate transport fleets, customs clearance, and equitable field distributions.</li></ul>',
                'lang' => 'en',
                'level' => Level::Intermediate,
                'is_free' => false,
                'price' => 175.00,
                'old_price' => 260.00,
                'order' => 16,
                'sections' => [
                    [
                        'title' => 'Module 1: Principles of Humanitarian Supply Chains',
                        'lessons' => [
                            [
                                'title' => 'The Humanitarian Supply Chain Framework & Cluster Coordination',
                                'content' => "Introduction to emergency logistics mandates, Sphere standards for shelter and NFIs, and inter-agency coordination via the UN Global Logistics Cluster.",
                                'duration' => 20,
                            ],
                            [
                                'title' => 'Logistics Preparedness and Rapid Needs Assessment',
                                'content' => "Assessing local infrastructure, supply corridors, port capacities, market availability, and bottleneck risks during early-stage crises.",
                                'duration' => 25,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Module 2: Emergency Procurement and Donor Compliance',
                        'lessons' => [
                            [
                                'title' => 'Procurement Thresholds, Sourcing, and Vendor Vetting',
                                'content' => "Navigating competitive bidding, simplified emergency thresholds, vendor due diligence, and anti-fraud safeguards under USAID/ECHO guidelines.",
                                'duration' => 30,
                            ],
                            [
                                'title' => 'Incoterms, Customs Clearance, and International Shipping',
                                'content' => "Managing international freight documentation (Airway Bills, Bills of Lading), tax exemption waivers, and navigating customs clearance hurdles.",
                                'duration' => 25,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Module 3: Warehousing, Fleet Management & Last-Mile Distribution',
                        'lessons' => [
                            [
                                'title' => 'Warehouse Safety, Inventory Control & Cold Chain Storage',
                                'content' => "Establishing temporary storage facilities, stock reconciliation, bin cards, FIFO/FEFO principles, and cold chain protocols for medical supplies.",
                                'duration' => 30,
                            ],
                            [
                                'title' => 'Fleet Management, Fuel Security and Route Optimization',
                                'content' => "Vehicle allocation, GPS tracking systems, driver safety in volatile environments, and fuel conservation protocols.",
                                'duration' => 25,
                            ],
                            [
                                'title' => 'Safe, Dignified Field Distributions and Modality Selection',
                                'content' => "Designing distribution sites, crowd control, token/biometric verification, and deciding between in-kind items and cash & voucher assistance (CVA).",
                                'duration' => 25,
                            ],
                        ],
                    ],
                ],
            ],

            [
                'tutor_email' => 'tutor3@ngoacademy.test', // Dr. Emily Chen (user_id: 768)
                'category_slug' => 'project-management',
                'title' => 'Winning Grant Proposals: Donor Engagement & Proposal Writing',
                'description' => 'A practical, step-by-step masterclass on deciphering donor guidelines, writing compelling problem statements, developing evidence-based project concepts, and assembling winning technical and financial proposals for major international donors.',
                'objectives' => '<ul><li>Analyze donor priorities, guidelines, and evaluation rubrics (USAID, EU/ECHO, UN, FCDO).</li><li>Craft persuasive statements of need, theories of change, and detailed activity matrices.</li><li>Construct compliant, unit-based project budgets with narrative justifications.</li><li>Conduct structured peer reviews (Red Team Reviews) to maximize success rates.</li></ul>',
                'lang' => 'en',
                'level' => Level::Beginner,
                'is_free' => true,
                'price' => 0.00,
                'old_price' => null,
                'order' => 17,
                'sections' => [
                    [
                        'title' => 'Module 1: Donor Intelligence and Opportunity Assessment',
                        'lessons' => [
                            [
                                'title' => 'Mapping Institutional Donors & Deciphering RFPs/Calls for Proposals',
                                'content' => "Navigating donor portals (Grants.gov, EU Funding & Tenders, UN Partner Portal), evaluating eligibility, and conducting Go/No-Go decision analysis.",
                                'duration' => 25,
                            ],
                            [
                                'title' => 'Building Strategic Consortia and Local Partner Alignment',
                                'content' => "Structuring partner agreements, teaming memoranda of understanding (MOUs), and establishing equitable consortia governance.",
                                'duration' => 20,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Module 2: Technical Narrative Architecture',
                        'lessons' => [
                            [
                                'title' => 'Writing High-Impact Needs Statements & Problem Analyses',
                                'content' => "Using verifiable statistical data, participatory assessments, and vulnerability criteria to substantiate urgency and relevance.",
                                'duration' => 30,
                            ],
                            [
                                'title' => 'Articulating the Theory of Change & Results Framework',
                                'content' => "Structuring clean causal pathways, linking outcomes to activities, and mainstreaming gender equality and environmental sustainability.",
                                'duration' => 30,
                            ],
                            [
                                'title' => 'Developing the Project Management, Staffing & Risk Plan',
                                'content' => "Outlining organograms, key personnel qualifications, duty of care protocols, and project risk mitigation strategies.",
                                'duration' => 25,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Module 3: Budgeting, Red Team Review & Post-Submission',
                        'lessons' => [
                            [
                                'title' => 'Building the Cost Proposal and Budget Narrative',
                                'content' => "Formulating unit-based personnel, travel, equipment, and direct operational costs alongside indirect cost recovery rates (NICRA/ICR).",
                                'duration' => 30,
                            ],
                            [
                                'title' => 'The Red Team Review: Scoring Proposals Against Donor Criteria',
                                'content' => "Conducting internal peer reviews, compliance matrix checking, proofreading, and final submission packaging.",
                                'duration' => 25,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($coursesData as $cData) {
            $user = User::where('email', $cData['tutor_email'])->first();
            $category = Category::where('slug', $cData['category_slug'])->first();

            if (!$user || !$category) {
                continue;
            }

            $course = Course::updateOrCreate(
                [
                    'title' => $cData['title'],
                ],
                [
                    'slug' => Str::slug($cData['title']),
                    'description' => $cData['description'],
                    'objectives' => $cData['objectives'],
                    'lang' => $cData['lang'],
                    'level' => $cData['level'],
                    'status' => CourseStatus::published,
                    'is_free' => $cData['is_free'],
                    'price' => $cData['price'],
                    'old_price' => $cData['old_price'],
                    'category_id' => $category->id,
                    'tutor_id' => $user->id,
                    'delivery_mode' => 'standard',
                    'theme' => 'classic',
                    'order' => $cData['order'],
                    'duration' => 0,
                ]
            );

            CoursePrice::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'is_active' => true,
                ],
                [
                    'price' => $cData['price'],
                ]
            );

            $totalCourseDuration = 0;
            $sectionOrder = 1;

            foreach ($cData['sections'] as $sData) {
                $section = CourseSection::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'title' => $sData['title'],
                    ],
                    [
                        'order' => $sectionOrder,
                        'status' => CourseStatus::published,
                        'duration' => 0,
                    ]
                );

                $totalSectionDuration = 0;
                $lessonOrder = 1;

                foreach ($sData['lessons'] as $lData) {
                    $lessonOverallOrder = ($sectionOrder * 100) + $lessonOrder;

                    Lesson::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'section_id' => $section->id,
                            'title' => $lData['title'],
                        ],
                        [
                            'public_key' => (string) Str::uuid(),
                            'content' => $lData['content'],
                            'duration' => $lData['duration'],
                            'lesson_order' => $lessonOrder,
                            'section_order' => $sectionOrder,
                            'status' => CourseStatus::published,
                            'is_ready' => true,
                            'tutor_id' => $user->id,
                            'retake_limit' => 3,
                            'cooldown_minutes' => 1440,
                            'pass_percent' => 100,
                        ]
                    );

                    $totalSectionDuration += $lData['duration'];
                    $lessonOrder++;
                }

                $section->update(['duration' => $totalSectionDuration]);
                $totalCourseDuration += $totalSectionDuration;
                $sectionOrder++;
            }

            $course->update(['duration' => $totalCourseDuration]);
        }
    }

    /**
     * Update tutor profiles with professional and realistic names & specializations.
     */
    protected function updateTutorProfiles(): void
    {
        $profiles = [
            'tutor1@ngoacademy.test' => [
                'name_en' => 'Dr. Sarah Mitchell',
                'specialization' => 'القيادة التنفيذية والحوكمة المؤسسية',
                'specialization_en' => 'Executive Leadership & NGO Governance',
                'job_title' => 'خبير الحوكمة والقيادة الاستراتيجية',
                'job_title_en' => 'Senior Governance & NGO Strategy Expert',
            ],
            'tutor2@ngoacademy.test' => [
                'name_en' => 'Prof. Ahmed Hassan',
                'specialization' => 'إدارة المشاريع التنموية والإنسانية',
                'specialization_en' => 'Development & Humanitarian Project Management',
                'job_title' => 'استشاري إدارة المشاريع التنموية',
                'job_title_en' => 'Senior Project Management Consultant',
            ],
            'tutor3@ngoacademy.test' => [
                'name_en' => 'Dr. Emily Chen',
                'specialization' => 'تطوير المنح وكتابة المقترحات التمويلية',
                'specialization_en' => 'Grant Acquisition & Proposal Development',
                'job_title' => 'مستشارة استقطاب التمويل وتطوير المنح',
                'job_title_en' => 'Senior Resource Mobilization Specialist',
            ],
            'tutor4@ngoacademy.test' => [
                'name_en' => 'Dr. Omar Al-Rashid',
                'specialization' => 'المتابعة والتقييم والمساءلة والتعلم (MEAL)',
                'specialization_en' => 'Monitoring, Evaluation, Accountability & Learning',
                'job_title' => 'خبير نظم المتابعة والتقييم وقياس الأثر',
                'job_title_en' => 'MEAL & Impact Evaluation Specialist',
            ],
            'tutor6@ngoacademy.test' => [
                'name_en' => 'Dr. James Wilson',
                'specialization' => 'اللوجستيات وسلاسل الإمداد الإنسانية',
                'specialization_en' => 'Humanitarian Logistics & Emergency Supply Chain',
                'job_title' => 'خبير الإمداد واللوجستيات في الطوارئ',
                'job_title_en' => 'Emergency Logistics Coordinator',
            ],
            'tutor7@ngoacademy.test' => [
                'name_en' => 'Dr. Fatima Al-Sayed',
                'specialization' => 'حماية الطفل وصون الفئات المستضعفة',
                'specialization_en' => 'Child Protection & Safeguarding in Emergencies',
                'job_title' => 'أخصائية حماية وصون الطفل',
                'job_title_en' => 'Child Protection Specialist',
            ],
        ];

        foreach ($profiles as $email => $data) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                continue;
            }

            $user->update([
                'job_title' => $data['job_title'],
                'job_title_en' => $data['job_title_en'],
            ]);

            Tutor::where('user_id', $user->id)->update([
                'name_en' => $data['name_en'],
                'specialization' => $data['specialization'],
                'specialization_en' => $data['specialization_en'],
                'is_active' => true,
            ]);
        }
    }
}
