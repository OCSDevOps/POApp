# Reference-Only Archive

This folder keeps historical docs and retired tests that are no longer the current source of truth.

Do not use these files for current app status, database status, or active test coverage.

Use these files instead:
- `../../README.md`
- `../../CURRENT_STATE_REPORT.md`
- `../../DATABASE_STATUS.md`
- `../../../tests/TEST_SUITE_README.md`

Archive notes:
- `docs/archive/reference_only/docs/` contains old phase reports, setup notes, and status snapshots kept for reference.
- `docs/archive/reference_only/tests/` contains retired placeholder tests and boilerplate examples kept only to preserve history.
- Files in this archive are intentionally outside the active `tests/` tree so PHPUnit will not discover them.
