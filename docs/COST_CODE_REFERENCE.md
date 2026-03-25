# Cost Code Reference

Last updated: 2026-03-21

## Source

This project's active cost code hierarchy now follows the structure extracted from the attached reference file:

- `Standrad Cost Code List As of March 2020.PDF`
- Original local source path used during this review:
  `C:\Users\OCS\OneDrive - SYS Accounting\Desktop\Standrad Cost Code List As of March 2020.PDF`

## Active Hierarchy Pattern

The hierarchy editor and tests now use a consistent three-segment format:

- Level 1: section headers like `1-00-00`, `2-00-00`, `3-00-00`
- Level 2: categories like `2-01-00`, `2-03-00`, `2-16-00`
- Level 3: detail codes like `2-03-10`, `2-03-30`, `2-16-10`

## Standard Top-Level Sections

- `1-00-00` Land
- `2-00-00` Hard Construction Costs
- `3-00-00` Soft Construction Costs
- `4-00-00` Sales & Marketing
- `5-00-00` Contingencies

## Common Category Examples

- `2-01-00` General Conditions
- `2-02-00` Site Work
- `2-03-00` Concrete
- `2-05-00` Metals
- `2-06-00` Wood & Plastics
- `2-15-00` Mechanical
- `2-16-00` Electrical
- `3-17-00` Consultants
- `3-19-00` Municipal Levies
- `3-20-00` Financing

## Common Detail Examples

- `2-03-10` Formwork Material
- `2-03-20` Concrete Reinforcing (rebar)
- `2-03-30` Concrete Supply
- `2-05-10` Structural Steel Frame
- `2-15-40` Plumbing Work
- `2-16-10` Electrical Work
- `3-17-05` Architects
- `5-22-01` Construction Contingency

## Current App Usage

- The admin hierarchy editor is aligned to this standard.
- Hierarchy edits now cascade section and category code changes to descendants.
- The extracted catalog is now stored in `database/data/standard_cost_codes_march_2020.json`.
- Seeded tenants and newly created companies now receive the full March 2020 list plus six reusable template packs derived from this reference.
