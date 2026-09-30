Here is a clean, structured overview of the **HR Single Source** project designed for you to copy and paste directly into your CLI or agent setup:

```markdown
# Project Overview: HR Single Source (SD Worx Track)

## Concept Summary
A centralized, smart document portal and semantic search engine that acts as the single source of truth for company HR policies, employment contracts, and compliance files. It eliminates version fragmentation and enables employees and HR managers to instantly retrieve accurate, verified information.

## Core Features
1. **Admin Document Ingestion & Version Control:** 
   - HR upload portal for PDFs, Word docs, and policies.
   - Automatic metadata tagging (Department, Date, Version Number).
   - Auto-archiving of older document versions to ensure only the single source of truth is active.
2. **Natural Language Search / Knowledge Hub:**
   - Search bar for employees/managers to query HR policies using plain text (e.g., "What is our remote work policy?").
   - Instant extraction and display of the exact source paragraph, document name, and version.
3. **Role-Based Access Guardrails:**
   - Secure authorization checks to ensure regular employees cannot access restricted HR-only files (prepared for Aikido security auditing).

## Tech Stack & Architecture (Rapid 4-Hour Hackathon Build)
- **Frontend / UI:** Python Streamlit or a lightweight React/Next.js setup (scaffolded via Cursor).
- **Backend & Storage:** Local JSON metadata index + file storage folder on Google Cloud / local environment.
- **Search Logic:** Lightweight semantic or keyword-matching text search engine.
- **Security:** Scanned and verified using Aikido AI Code Audit to prevent IDOR and authorization flaws.

## Team Execution Tasks
- **Person 1 (Frontend):** Build the Dashboard UI (HR Admin Upload View vs. Employee Search View).
- **Person 2 (Backend):** Implement file upload handlers, storage logic, and version-control auto-archiving.
- **Person 3 (Search/AI):** Write the document text extraction and natural language query-matching engine.
- **Person 4 (Security & Submission):** Connect to GitHub, run Aikido security audits, fix vulnerabilities, capture screenshots, and record the demo video.

```
