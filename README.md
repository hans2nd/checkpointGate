# Checkpoint GIIC

A web application for managing and monitoring checkpoint gate data, vehicle types, goods, and employee activities at GIIC.

## Recent Updates & UI Modernization

The application has recently undergone a comprehensive UI/UX overhaul to provide a modern, premium, and interactive experience for users.

### Key Enhancements:

1. **Dashboard**
   - **Interactive Charts**: Clickable charts that act as filters for other visual data on the dashboard.
   - **Top Matrices**: Added new tracking matrices for the most frequent Goods Types and Vehicle Types.
   - **Loading & Gate Analytics**: Introduced cards displaying the longest vehicle loading times and the most frequently used gates.
   - **Localization Support**: Dashboard content is now fully translated and available in English, Indonesian, and Japanese.

2. **Report Menu**
   - Redesigned with a modern, card-based layout (`checkpoint-table-card`).
   - Enhanced filter controls (`checkpoint-filter-card`) with a non-auto-refresh behavior for better UX. 
   - The export to Excel button has been moved directly into the filter form for easier access.
   
3. **Checkpoint Data**
   - Modernized the Index, Show, and Edit pages to follow the new premium design language.
   - Integrated intuitive **SweetAlert** pop-ups for action confirmations (e.g., delete validations) providing safety and better visual feedback.
   - **Cross Dock Automation**:
     - *Inbound Cross Dock* transactions automatically bypass the "Start Loading" and "Document Handover (Serah Dokumen)" actions.
     - Upon completion (End Loading), a new *Outbound Cross Dock* transaction is automatically generated, inheriting identical details (vehicle, vendor, shipping type, goods type, Delivery Note, and PO) to streamline the gate-out process.
     - Automatically excludes strict form validations (Delivery Note, PO Vendor, and Receipt Number uniqueness) for Cross Dock activities, allowing for a faster workflow.
   - **Dynamic Document Receipt Modal**: 
     - Restructured form to prioritize Activity selection (Inbound/Outbound) upfront.
     - Dynamically updates *Shipping Type* options, *Type Of Load*, and UI labels based on the selected Activity.
     - Auto-adjusts form validations, enforcing mandatory *Purchase Order* or *Delivery Note (Surat Jalan)* inputs depending on the specific activity and shipping configuration.
   - **Enhanced Excel Import**: 
     - Expanded template mapping to cover transaction fields: Date, Vehicle Plate Number, Vendor, Vehicle Type, Product Type Storage, Activity, Delivery Note (Surat Jalan), Purchase Order, and Note.
     - **Smart Lookup (Auto-Mapping)**: Automatically retrieves Vendor, Vehicle Type, and Driver from the Vehicle Master based on the vehicle's plate number.
     - **Auto-Insert Master**: Automatically registers new, unrecognized vehicles directly into the Vehicle Master database during the import process.
     - **Row-Level Error Validation**: Replaced silent error-skipping with detailed row-by-row validation using SweetAlert pop-ups, pointing out exactly which rows are missing required data or contain invalid formats.
   
4. **Master Data Management**
   - Applied the new premium design language across **Vehicle Types**, **Vehicle Master**, and **Employee Master** modules.
   - **Forms (Create/Edit)**: Upgraded with `premium-form-card`, featuring elegant dark-to-orange gradients, soft shadows, and interactive `premium-input` fields.
   - **Tables (Index)**: Upgraded list views with clear borders, hover row effects (`hover:bg-orange-50/30`), and responsive pagination.

## Tech Stack

- **Backend**: Laravel (PHP)
- **Frontend**: Blade Templating, Tailwind CSS
- **Interactivity**: Vanilla JavaScript, Chart.js (Dashboard), SweetAlert2 (Popups/Validations)

---

## Update History

- **September 15, 2026** — *Updated*
  - Added Cross Dock full automation (auto-complete Inbound actions, auto-create Outbound).
  - Bypassed strict validation rules (Surat Jalan, PO Vendor, Receipt Number) exclusively for Cross Dock types.
  - Fixed UI modal logic to preserve the correct 'Cross Dock' type of load during subsequent Outbound transactions.
  - **Document Handover (Serah Dokumen)**: Restricted *Receipt Number* input to be mandatory *only* for Inbound activities. Completely hides the input field for Outbound activities.
  - **VPS Synchronization Fix**: Created a new database migration to ensure the local `checkpoints_giic` table accurately mirrors the new columns (`type_of_load`, `product_category_id`, `shipping_type`, `is_cross_dock`, `is_generated_cross_dock`).
  - Validated API compatibility for `SyncApiController` to handle dynamic attribute payload pushes without errors.
  - **PowerBI Reporting**: Updated `ReportApiController` to expose `is_cross_dock` and `is_generated_cross_dock` boolean flags as part of the JSON dataset.

- **September 13, 2026** — *Created/Updated by Hans*
  - Comprehensive UI/UX overhaul (Dashboard, Report Menu, Master Data).
  - Added smart Excel import feature (auto-mapping & auto-insert).
  - Updated dynamic logic for the **Document Receipt (Penerimaan Dokumen)** modal (Inbound/Outbound activity, Delivery Note & PO validations).
