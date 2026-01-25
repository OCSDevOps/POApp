<div class="row g-3">
    <div class="col-md-12">
        <label class="form-label">Template Name</label>
        <input type="text" name="pt_template_name" id="pt_template_name" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">PO (pt_t_porder)</label>
        <input type="number" name="pt_t_porder" id="pt_t_porder" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">RO (pt_t_rorder)</label>
        <input type="number" name="pt_t_rorder" id="pt_t_rorder" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">RC Order (pt_t_rcorder)</label>
        <input type="number" name="pt_t_rcorder" id="pt_t_rcorder" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">RFQ (pt_t_rfq)</label>
        <input type="number" name="pt_t_rfq" id="pt_t_rfq" class="form-control" min="0" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">Items</label>
        <input type="number" name="pt_m_item" id="pt_m_item" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">UOM</label>
        <input type="number" name="pt_m_uom" id="pt_m_uom" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Cost Codes</label>
        <input type="number" name="pt_m_costcode" id="pt_m_costcode" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Projects</label>
        <input type="number" name="pt_m_projects" id="pt_m_projects" class="form-control" min="0" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">Suppliers</label>
        <input type="number" name="pt_m_suppliers" id="pt_m_suppliers" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tax Groups</label>
        <input type="number" name="pt_m_taxgroup" id="pt_m_taxgroup" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Budget</label>
        <input type="number" name="pt_m_budget" id="pt_m_budget" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Email Templates</label>
        <input type="number" name="pt_m_email" id="pt_m_email" class="form-control" min="0" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">Item Import</label>
        <input type="number" name="pt_i_item" id="pt_i_item" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Item Pricing</label>
        <input type="number" name="pt_i_itemp" id="pt_i_itemp" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Supplier Catalog</label>
        <input type="number" name="pt_i_supplierc" id="pt_i_supplierc" class="form-control" min="0" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">Equipment</label>
        <input type="number" name="pt_e_eq" id="pt_e_eq" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Equipment Maintenance</label>
        <input type="number" name="pt_e_eqm" id="pt_e_eqm" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Checklists</label>
        <input type="number" name="pt_e_checklist" id="pt_e_checklist" class="form-control" min="0" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">Users</label>
        <input type="number" name="pt_a_user" id="pt_a_user" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Permissions</label>
        <input type="number" name="pt_a_permissions" id="pt_a_permissions" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Company Info</label>
        <input type="number" name="pt_a_cinfo" id="pt_a_cinfo" class="form-control" min="0" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Procore</label>
        <input type="number" name="pt_a_procore" id="pt_a_procore" class="form-control" min="0" required>
    </div>

    <div class="col-md-12">
        <label class="form-label">Template Users (IDs, comma separated)</label>
        <input type="text" name="pt_template_users" id="pt_template_users" class="form-control" placeholder="e.g. 2,3,5">
        <small class="text-muted">Stored as JSON array.</small>
    </div>
</div>
