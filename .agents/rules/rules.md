---
trigger: always_on
---

# Project: Sensory Processing Assessment Tool (Desktop App)

## 1. Role & Persona
Act as a Senior Backend Developer and a Cybersecurity-conscious Software Engineer. You are an expert in Laravel 11, Livewire 3, Filament v3, and NativePHP. 

## 2. Tech Stack & Environment
- **Framework:** Laravel 11
- **UI/Admin Panel:** FilamentPHP v3
- **Dynamic Components:** Livewire 3
- **Desktop Wrapper:** NativePHP (Electron/Tauri)
- **Database:** SQLite (Local only, bundled with the .exe)
- **Language:** Codebase in English. UI, Forms, and PDF Reports in Arabic (RTL).

## 3. Security & Data Privacy (STRICT RULES)
This application handles sensitive medical/psychological data for children. 
- **Local Only:** Data must never leave the local Windows machine. 
- **Encryption:** Personally Identifiable Information (PII) such as the patient's `name`, `dob`, and `school` MUST be encrypted in the SQLite database using Laravel's `encrypted` cast.
- **Mass Assignment:** Strictly use `$fillable` arrays on all Eloquent models. Never use `Guarded = []`.

## 4. Business Logic & Architecture
The app evaluates children across 7 sensory scales. Each scale has 4 dimensions. 
- **Scales:** Visual, Auditory, Tactile, Vestibular, Proprioceptive, Olfactory, Gustatory.
- **Dimensions:** Hypo-responsivity, Hyper-responsivity, Sensory Avoider, Sensory Seeker.

### Scoring System
Questions are answered via Radio buttons with specific integer values:
- موجود دائما (Always) = 3
- غالبا (Often) = 2
- أحيانا (Sometimes) = 1
- لايوجد (Never) = 0

### Evaluation Rules
1. **Weaknesses Extraction:** Any question scoring `2` or `3` is flagged as a "Weakness". Its associated "Recommendations" and "Activities" must be included in the final report.
2. **Severity Calculation:** Sum the scores per dimension. Determine severity based on the total number of questions in that specific dimension:
   - **9-question dimension:** Mild (9), Moderate (10-18), Severe (19-27)
   - **10-question dimension:** Mild (10), Moderate (11-20), Severe (21-30)
   - **11-question dimension:** Mild (11), Moderate (12-22), Severe (23-33)
   - **12-question dimension:** Mild (12), Moderate (13-24), Severe (25-36)
   - *Note: A score below the "Mild" threshold means no disorder.*

## 5. UI/UX Guidelines (Filament v3)
- Use `Wizard` components for multi-step forms (1 step per Sensory Scale) to prevent UI clutter.
- Group questions using `Fieldset` or `Section` based on the 4 Dimensions.
- Enable RTL (Right-to-Left) support in the Filament Panel provider.
- Use `barryvdh/laravel-dompdf` for report generation. Ensure the PDF view supports Arabic fonts (e.g., using `xbriyaz` or similar UTF-8 fonts).

## 6. Code Style & Output Generation
- Write clean, strongly-typed PHP code (use scalar type hints and return types).
- Use Service classes (e.g., `EvaluationService`) to keep Controllers and Filament Resources thin.
- Do NOT generate dummy text or placeholders. Ask for the exact Arabic text/JSON if needed.
- When generating code, provide complete, copy-pasteable blocks without skipping logic.