Understood, let's drop the blockchain angle entirely and focus on a fast, high-impact prototype that you can realistically build, secure, and polish in your remaining time.

Since the core problem is a **central source of truth for files** within an HR ecosystem like SD Worx, let's pivot to a streamlined, AI-powered document intelligence hub.

---

### Streamlined Idea: "HR Single Source" (The Master Document & Policy Hub)

* **The Concept:** A centralized, smart document portal where all official company policies, employment contracts, and employee-specific documents live.
* **The Problem It Solves:** HR departments deal with massive file fragmentation. Employees look at old versions of handbooks, managers can't find specific contract clauses, and compliance is a nightmare.
* **How It Works (The 4-Hour Build):**
1. **Central Repository Dashboard:** Build a simple web interface (using Python/Streamlit or a light framework via **Cursor**) where HR can upload core documents (PDFs, policies, contracts).
2. **Smart Ingestion & Indexing:** When a file is uploaded, the app reads it, extracts text, and tags it by department, version number, and validity date.
3. **The "Single Source of Truth" Query Engine:** Instead of searching through folders, users (employees or HR) type a natural language question into a search bar. The app searches *only* the official, latest-version repository and gives an exact citation/snippet from the document.
4. **Version Control Guardrails:** If HR uploads a *new* version of a document, the system automatically archives the old one, ensuring nobody accidentally references outdated policies.



### Why this is a great hackathon choice:

* **Achievable in < 4 hours:** You don't need complex distributed ledgers; you just need a clean file upload system, a local database or vector search, and a neat UI.
* **Fits SD Worx perfectly:** It directly addresses the "central source of truth for files" pain point by keeping enterprise documents unified, version-controlled, and instantly searchable.
* **Easy to secure for Aikido:** Because it's a straightforward file management and search app, running your code through **Aikido** to check for authorization or access control flaws (making sure regular employees can't search or view HR-only master files) will be quick and straightforward.

How does this streamlined, AI-powered document repository sound for your team's build?
