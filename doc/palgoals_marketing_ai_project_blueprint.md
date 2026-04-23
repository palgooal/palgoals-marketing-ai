## Progress Note

- Step 9 completed.
- Offers generation foundation added.
- Next step: planner foundation or shared AI generation UX improvements.
- Step 10 completed.
- Planner foundation added.
- Next step: analyzer foundation or shared AI generation UX consolidation.
- Step 11 completed.
- Analyzer foundation added.
- Next step: shared AI generation UX consolidation and internal logs visibility.
- Step 12 completed.
- AI UX consolidation and internal logs visibility added.
- Next step: prompt template improvements or first prompt governance/versioning polish.
- Step 13 completed.
- Prompt template governance and usability polish added.
- Next step: review/approval-lite for generated outputs or prompt versioning groundwork.
- Step 14 completed.
- Review/approval-lite added for generated outputs.
- Next step: prompt versioning groundwork or export/copy usability polish.
- Step 15 completed.
- Export/copy usability polish added.
- Next step: prompt versioning groundwork or lightweight output publishing workflow.
- Step 16 completed.
- Prompt versioning groundwork added.
- Next step: lightweight output publishing workflow or prompt history/revert polish.
- Step 17 completed.
- Lightweight output publishing workflow added.
- Next step: prompt history/revert polish or internal content package/export improvements.
- Step 18 completed.
- Prompt history preview and revert polish added.
- Next step: internal content package/export improvements or prompt comparison polish.
- Step 19 completed.
- Internal content package/export improvements added.
- Step 20 completed.
- Lightweight prompt usage insights added.
- Step 21 completed.
- Lightweight prompt comparison polish added.
- Step 22 completed.
- Internal AI workflow health insights added.
- Next step: lightweight prompt lifecycle cleanup or AI request retry-lite investigation UX.

# Palgoals Marketing AI — Project Blueprint v1

## Vision

بناء نظام تسويق ذكي احترافي خاص بشركة **Palgoals** في المرحلة الأولى، وقابل للتحول لاحقًا إلى منتج SaaS متعدد الشركات.

الهدف من النظام:

- العمل كمسوّق إلكتروني داخلي لشركة Palgoals
- توليد المحتوى التسويقي والإعلانات والعروض والخطط
- تحليل الصفحات التسويقية وتحسين التحويل
- بناء قاعدة معرفة تسويقية مركزية للشركة
- تجهيز البنية لاحقًا لإضافة Chatbot مبيعات وربطه بالمنصة

---

## Phase 1 Scope

النسخة الأولى ستكون مخصصة لـ **Palgoals فقط**، مع تصميم داخلي قابل للتوسع لاحقًا.

### In Scope

- Authentication + Admin Dashboard
- Brand Knowledge Base
- Content Engine
- Offers Engine
- Marketing Planner
- Website / Landing Page Analyzer
- AI Gateway Layer
- Prompt Template Management
- Generation History + Logs

### Out of Scope (Later)

- Multi-tenant SaaS onboarding
- Subscription billing for external companies
- White-label support
- Team workspace for multiple client organizations
- Public self-service onboarding
- Advanced chatbot sales assistant

---

## Recommended Tech Stack

- **Framework:** Laravel 12
- **Admin UI:** Blade + Tailwind CSS v4.1
- **Database:** MySQL / MariaDB
- **Queue:** Redis + Laravel Queue
- **Cache:** Redis
- **AI Providers:** OpenAI first, Claude-ready architecture
- **Storage:** Local / S3-compatible
- **Auth:** Laravel Breeze or custom admin auth
- **Permissions:** Spatie Laravel Permission (recommended)
- **Monitoring:** Laravel Telescope (dev), custom AI request logs

---

## Project Name

### Recommended repository / app name

`palgoals-marketing-ai`

### Suggested internal product name

- Palgoals Marketing AI
- Palgoals Growth OS
- Palgoals AI Marketer

---

## High-Level Architecture

### Core Layers

1. **Presentation Layer**
    - Dashboard
    - Content generation screens
    - Planning screens
    - Analyzer screens
    - Knowledge management screens

2. **Application Layer**
    - Use cases / Actions
    - GenerateContentAction
    - GenerateOfferAction
    - BuildMarketingPlanAction
    - AnalyzePageAction

3. **Domain Layer**
    - Brand profile rules
    - Offer strategy rules
    - Campaign logic
    - Content objective definitions
    - Prompt composition rules

4. **Infrastructure Layer**
    - AI provider clients
    - Queue jobs
    - Cache services
    - Database repositories
    - Logging / metrics

---

## Main Modules

### 1) Organization Module

في المرحلة الأولى سيكون هناك سجل واحد فقط يمثل Palgoals.

#### Responsibilities

- تعريف الكيان الأساسي للشركة
- ربط كل البيانات التسويقية به
- تمهيد التوسع لاحقًا إلى SaaS

### 2) Brand Profile Module

يمثل هوية Palgoals التسويقية.

#### Includes

- اسم العلامة
- الوصف
- الرؤية
- الرسالة
- tone of voice
- الجمهور المستهدف
- الأسواق المستهدفة
- نقاط البيع الفريدة (USP)
- الاعتراضات الشائعة والردود
- CTA preferences

### 3) Services & Templates Knowledge Module

قاعدة معرفة للخدمات والقوالب والفئات.

#### Includes

- الخدمات
- القوالب
- الفئات
- الأسعار
- الفوائد
- المشاكل التي تحلها
- أمثلة الاستخدام

### 4) Content Engine Module

توليد المحتوى التسويقي.

#### Outputs

- Social media posts
- Ad copy
- Headlines
- CTA suggestions
- Landing page copy
- Email copy
- WhatsApp marketing copy

### 5) Offers Engine Module

إنشاء عروض تسويقية وتجارية.

#### Outputs

- Limited-time offers
- Bundle offers
- Discount logic suggestions
- Seasonal campaigns
- Urgency messaging

### 6) Marketing Planner Module

إنشاء خطط تسويقية.

#### Outputs

- Weekly marketing plan
- Monthly campaign plan
- Content calendar
- Priority recommendations
- Channel strategy suggestions

### 7) Page Analyzer Module

تحليل الصفحات التسويقية.

#### Inputs

- رابط صفحة
- محتوى HTML أو نص يدوي
- وصف المنتج أو الخدمة

#### Outputs

- Conversion issues
- Copy weaknesses
- CTA recommendations
- Funnel friction notes
- Suggested structural improvements

### 8) AI Gateway Module

طبقة وسيطة بين التطبيق ومزوّدي الذكاء الاصطناعي.

#### Responsibilities

- اختيار الموديل المناسب حسب المهمة
- إدارة الـ prompts
- تتبع الاستخدام والتكلفة
- تسجيل الطلبات والاستجابات
- دعم إضافة Claude لاحقًا بسهولة

### 9) Prompt Template Module

إدارة قوالب التعليمات لكل مهمة.

#### Examples

- Generate social post
- Generate ad campaign
- Analyze landing page
- Suggest offer
- Build weekly plan

### 10) History & Logs Module

لحفظ كل عمليات التوليد والتحليل.

#### Includes

- الطلب الأصلي
- الإعدادات
- المخرجات
- الموديل المستخدم
- الزمن
- التكلفة التقريبية
- حالة التنفيذ

---

## Suggested Folder Structure

```text
app/
├── Actions/
│   ├── Content/
│   ├── Offers/
│   ├── Plans/
│   └── Analysis/
├── DTOs/
├── Enums/
├── Http/
│   ├── Controllers/
│   │   ├── Dashboard/
│   │   ├── Content/
│   │   ├── Offers/
│   │   ├── Plans/
│   │   ├── Analysis/
│   │   └── Settings/
│   ├── Requests/
│   └── Middleware/
├── Models/
├── Repositories/
├── Services/
│   ├── AI/
│   │   ├── Contracts/
│   │   ├── Providers/
│   │   ├── Prompting/
│   │   └── Routing/
│   ├── Brand/
│   ├── Content/
│   ├── Offers/
│   ├── Plans/
│   └── Analysis/
├── Support/
│   ├── PromptBuilders/
│   ├── PageAnalysis/
│   └── Formatting/
└── Jobs/
```

---

## Database Design (Phase 1)

### 1. organizations

يمثل الشركة أو الكيان.

Suggested fields:

- id
- name
- slug
- status
- created_at
- updated_at

### 2. brand_profiles

الهوية التسويقية الأساسية.

Suggested fields:

- id
- organization_id
- brand_name
- short_description
- long_description
- tone_of_voice
- primary_language
- secondary_language
- target_markets_json
- usp_json
- objections_json
- cta_preferences_json
- created_at
- updated_at

### 3. brand_services

الخدمات التي تبيعها Palgoals.

Suggested fields:

- id
- organization_id
- title
- slug
- description
- audience
- benefits_json
- problems_solved_json
- pricing_notes
- status
- sort_order
- created_at
- updated_at

### 4. template_categories

فئات القوالب.

Suggested fields:

- id
- organization_id
- name
- slug
- description
- status
- sort_order
- created_at
- updated_at

### 5. templates

القوالب التسويقية/البيعية التي تقدمها الشركة.

Suggested fields:

- id
- organization_id
- template_category_id
- name
- slug
- description
- audience
- features_json
- benefits_json
- price
- sale_price
- status
- created_at
- updated_at

### 6. knowledge_documents

مستندات المعرفة التسويقية.

Suggested fields:

- id
- organization_id
- title
- type
- source
- content_longtext
- metadata_json
- is_active
- created_at
- updated_at

### 7. marketing_campaigns

سجل الحملات التسويقية.

Suggested fields:

- id
- organization_id
- title
- objective
- target_audience
- channel
- offer_summary
- status
- notes
- created_at
- updated_at

### 8. content_generations

مخرجات المحتوى.

Suggested fields:

- id
- organization_id
- marketing_campaign_id nullable
- type
- title
- input_json
- output_longtext
- language
- tone
- model_name
- provider_name
- status
- created_at
- updated_at

### 9. offer_generations

العروض التي تم توليدها.

Suggested fields:

- id
- organization_id
- title
- input_json
- output_longtext
- model_name
- provider_name
- status
- created_at
- updated_at

### 10. strategy_plans

الخطط الأسبوعية / الشهرية.

Suggested fields:

- id
- organization_id
- period_type
- title
- goals_json
- input_json
- output_longtext
- model_name
- provider_name
- status
- created_at
- updated_at

### 11. page_analyses

نتائج تحليل الصفحات.

Suggested fields:

- id
- organization_id
- page_title
- page_url
- page_type
- input_json
- findings_longtext
- recommendations_longtext
- score nullable
- model_name
- provider_name
- status
- created_at
- updated_at

### 12. ai_prompt_templates

قوالب التعليمات.

Suggested fields:

- id
- organization_id nullable
- key
- title
- description
- system_prompt_longtext
- user_prompt_template_longtext
- module
- version
- is_active
- created_at
- updated_at

### 13. ai_requests

سجل جميع طلبات الذكاء الاصطناعي.

Suggested fields:

- id
- organization_id
- module
- task_type
- provider_name
- model_name
- prompt_snapshot_longtext
- input_json
- output_longtext nullable
- tokens_input nullable
- tokens_output nullable
- estimated_cost nullable
- latency_ms nullable
- status
- error_message nullable
- created_at
- updated_at

### 14. settings

إعدادات عامة للتطبيق.

Suggested fields:

- id
- key
- value_longtext
- created_at
- updated_at

---

## Recommended Enums

- OrganizationStatusEnum
- CampaignChannelEnum
- CampaignObjectiveEnum
- ContentTypeEnum
- PlanPeriodEnum
- AnalysisStatusEnum
- AIProviderEnum
- AIRequestStatusEnum

---

## Route Structure (Web)

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', ...);

    Route::prefix('brand')->group(function () {
        Route::get('/', ...);
        Route::put('/', ...);
    });

    Route::prefix('services')->group(function () {
        Route::get('/', ...);
        Route::get('/create', ...);
        Route::post('/', ...);
        Route::get('/{service}/edit', ...);
        Route::put('/{service}', ...);
    });

    Route::prefix('templates')->group(function () {
        Route::get('/', ...);
        Route::get('/create', ...);
        Route::post('/', ...);
        Route::get('/{template}/edit', ...);
        Route::put('/{template}', ...);
    });

    Route::prefix('content')->group(function () {
        Route::get('/', ...);
        Route::get('/create', ...);
        Route::post('/generate', ...);
        Route::get('/history', ...);
        Route::get('/{generation}', ...);
    });

    Route::prefix('offers')->group(function () {
        Route::get('/', ...);
        Route::get('/create', ...);
        Route::post('/generate', ...);
        Route::get('/history', ...);
    });

    Route::prefix('plans')->group(function () {
        Route::get('/', ...);
        Route::get('/create', ...);
        Route::post('/generate', ...);
        Route::get('/history', ...);
    });

    Route::prefix('analysis')->group(function () {
        Route::get('/', ...);
        Route::get('/create', ...);
        Route::post('/run', ...);
        Route::get('/history', ...);
        Route::get('/{analysis}', ...);
    });

    Route::prefix('prompts')->group(function () {
        Route::get('/', ...);
        Route::get('/create', ...);
        Route::post('/', ...);
        Route::get('/{prompt}/edit', ...);
        Route::put('/{prompt}', ...);
    });

    Route::prefix('logs')->group(function () {
        Route::get('/ai-requests', ...);
    });
});
```

---

## First Dashboard Widgets

أول نسخة من لوحة التحكم يجب أن تعطي انطباع “مسوّق رقمي جاهز للعمل”.

### Suggested widgets

- Marketing summary today
- Quick generate post
- Quick generate ad
- Quick generate offer
- Quick weekly plan
- Latest page analysis
- Latest AI activity
- Suggested actions today

---

## AI Gateway Design

### Goal

فصل التطبيق عن مزود الذكاء الاصطناعي نفسه.

### Contracts

- AIProviderInterface
- PromptBuilderInterface
- AIModelRouterInterface

### Suggested provider classes

- OpenAIProvider
- ClaudeProvider (later)

### Suggested services

- AIRequestLoggerService
- PromptTemplateResolverService
- AIModelRouterService
- AIExecutionService

### Example routing logic

- Content generation → OpenAI
- Offer generation → OpenAI
- Strategy planning → OpenAI first
- Deep page analysis → Claude-ready later

---

## Suggested UI Sections

### Sidebar

- Dashboard
- Brand Profile
- Services
- Templates
- Knowledge Base
- Content Engine
- Offers Engine
- Marketing Planner
- Page Analyzer
- Prompt Templates
- AI Logs
- Settings

---

## Execution Roadmap

### Sprint 1 — Foundation

- Create Laravel project
- Install Tailwind
- Setup auth
- Setup admin layout
- Create organizations table
- Create brand_profiles table
- Create settings table
- Seed Palgoals organization

### Sprint 2 — Brand & Knowledge

- Brand profile CRUD
- Services CRUD
- Template categories CRUD
- Templates CRUD
- Knowledge documents CRUD

### Sprint 3 — AI Core

- Build AI Gateway contracts
- Add OpenAI provider
- Add AI request logging
- Add prompt templates management
- Add queued generation pipeline

### Sprint 4 — Content Engine

- Content generation form
- Post generation
- Ad generation
- CTA generation
- History view

### Sprint 5 — Offers Engine

- Offer generation form
- Bundle suggestions
- Seasonal offers
- Urgency copy generation
- History view

### Sprint 6 — Marketing Planner

- Weekly plan generation
- Monthly plan generation
- Goal-based planning
- Plan archive/history

### Sprint 7 — Page Analyzer

- Page URL input
- Manual text input fallback
- Analysis report view
- Recommendations output
- Score + issue categories

### Sprint 8 — Polish

- Dashboard widgets
- filtering/search
- export/copy UX
- approval workflow basics
- role permissions

---

## Suggested MVP Priorities

إذا أردنا النسخة الأولى تكون قوية ومفيدة بسرعة، فالأولوية تكون:

1. Brand Profile
2. Services / Templates Knowledge
3. AI Gateway
4. Content Engine
5. Offers Engine
6. Marketing Planner
7. Page Analyzer

---

## Important Product Principles

- لا نبني “مولد نصوص” فقط
- نبني “نظام تشغيل تسويقي”
- كل مخرج يجب أن يكون قابلًا للاستخدام التجاري
- يجب تسجيل كل عملية توليد وتحليل
- يجب فصل prompts عن الكود قدر الإمكان
- يجب أن تكون المعمارية Claude-ready دون تعقيد مبكر
- يجب أن تكون قاعدة البيانات SaaS-ready داخليًا حتى لو عندنا شركة واحدة فقط الآن

---

## Immediate Next Step

الخطوة التنفيذية التالية:
**إعداد الهيكل الأساسي للمشروع Laravel + أول migrations + أسماء Modules + ترتيب الشاشات**

بعد ذلك نبدأ مباشرة في:

1. Foundation
2. Brand Profile
3. AI Gateway
4. Content Engine

---

## Progress Update

- Step 1 completed
- Foundation initialized
- Next step: admin layout polish + sidebar + settings page
- Step 2 completed
- Admin shell + sidebar + settings page added
- Next step: brand knowledge module (services + template categories + templates)
- Step 3 completed
- Brand knowledge foundations added
- Next step: knowledge documents + light filtering + status polish
- Step 4 completed
- Knowledge documents and light list polish added
- Next step: pagination polish + reusable form partials + current organization helper
- Step 5 completed
- Pagination + reusable form partials + current organization helper added
- Next step: AI core foundation (OpenAI config + provider contract + AI request logging schema)
- Step 6 completed
- AI core foundation added
- Next step: prompt templates + first content generation workflow
- Step 7 completed
- Prompt templates and first content generation workflow added
- Next step: improve content workflow UX + content history filtering + reusable AI generation patterns
- Step 8 completed
- Content workflow polish and reusable generation pattern added
- Next step: offers generation foundation or planner foundation (choose one later)
