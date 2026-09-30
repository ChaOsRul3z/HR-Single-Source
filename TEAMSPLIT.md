To build and polish your **HR Single Source** document hub within your tight hackathon timeframe, dividing and conquering is essential.

Here is an optimized **4-person task split** designed to get everything coded, secured, and ready for submission:

---

### Team Task Breakdown (4 People)

#### Person 1: Frontend & UI Lead (The Interface)

* **Objective:** Build a clean, intuitive web application layout using a rapid prototyping framework (e.g., Streamlit in Python or a quick React/Next.js setup via **Cursor**).
* **Key Tasks:**
* Create a landing dashboard with two main views: *HR Admin Portal* (for uploading files and managing versions) and *Employee Search Portal* (for asking questions).
* Design a clear "Version Status" indicator (showing which document is active vs. archived) so the "single source of truth" concept is immediately obvious to judges.



#### Person 2: Backend & Ingestion Lead (The Core Logic)

* **Objective:** Handle document parsing, local storage organization, and text extraction.
* **Key Tasks:**
* Implement file upload handlers for PDFs, Word documents, and text files.
* Set up a lightweight local database (or JSON-based index) that maps files to metadata: *Title, Department, Upload Date, and Version Number*.
* Write the logic to automatically flag an older document as "Archived" when a new version of the same policy or contract is uploaded.



#### Person 3: Search & AI Integration Lead (The "Brain")

* **Objective:** Power the search engine so users can query the central repository using natural language.
* **Key Tasks:**
* Integrate a simple semantic or keyword search over the uploaded files so that typing a query (e.g., *"paternity leave rules"*) returns the exact matching paragraph and source document name.
* *Optional Stretch:* Use an LLM API or local search tool to generate a concise summary of the file snippet found.



#### Person 4: Security, Deployment & Submissions Lead (The Compliance & Video Manager)

* **Objective:** Ensure the code passes all security hurdles, runs smoothly, and prepare the final package for the Builderbase platform.


* **Key Tasks:**
* Connect the GitHub repository to **Aikido** early on to run the AI Code Audit. Fix any authorization or IDOR flaws (making sure regular users can't access HR-restricted master files).


* Take the required "before and after" Aikido screenshots.


* Record and edit the **< 3-minute demo video** showcasing the problem, the solution, and a quick live walkthrough.


* Compile the final project description and submit on time.





---

### Immediate Action Plan (Next 30 Minutes)

1. **Sync (5 mins):** Agree on the tech stack (Python/Streamlit is usually the fastest for a 4-hour hack).
2. **Scaffold (10 mins):** Person 1 & 2 spin up the basic repo template using **Cursor**.


3. **Execute (Rest of the time):** Build out the upload $\rightarrow$ store $\rightarrow$ search loop, while Person 4 sets up the Aikido security pipeline.



Which role or part of the stack would you like to dive into first?
