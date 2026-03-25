# POApp Core Services Test Suite

This comprehensive test suite covers the critical business logic for the POApp Purchase Order Management System.

## Test Structure

```
tests/
├── Unit/
│   └── Services/
│       ├── BudgetServiceTest.php         # 20+ tests for budget management
│       ├── PurchaseOrderServiceTest.php  # 20+ tests for PO, RFQ, and receiving
│       ├── ApprovalServiceTest.php       # 20+ tests for approval workflows
│       └── PoChangeOrderServiceTest.php  # 15+ tests for PO change orders
├── Feature/
│   └── Workflows/
│       ├── PurchaseOrderBudgetWorkflowTest.php  # End-to-end PO with budget
│       ├── ApprovalWorkflowTest.php             # Multi-level approval flows
│       └── BudgetChangeOrderWorkflowTest.php    # BCO complete workflow
├── Feature/
│   ├── MultiTenancyIsolationTest.php    # Existing - data isolation tests
│   └── ... (existing feature tests)
└── TEST_SUITE_README.md                 # This file
```

## Running the Tests

### Run All Tests
```bash
./vendor/bin/phpunit
```

### Run Unit Tests Only
```bash
./vendor/bin/phpunit --testsuite Unit
```

### Run Feature Tests Only
```bash
./vendor/bin/phpunit --testsuite Feature
```

### Run Specific Service Tests
```bash
./vendor/bin/phpunit tests/Unit/Services/BudgetServiceTest.php
./vendor/bin/phpunit tests/Unit/Services/PurchaseOrderServiceTest.php
./vendor/bin/phpunit tests/Unit/Services/ApprovalServiceTest.php
./vendor/bin/phpunit tests/Unit/Services/PoChangeOrderServiceTest.php
```

### Run Specific Workflow Tests
```bash
./vendor/bin/phpunit tests/Feature/Workflows/PurchaseOrderBudgetWorkflowTest.php
./vendor/bin/phpunit tests/Feature/Workflows/ApprovalWorkflowTest.php
./vendor/bin/phpunit tests/Feature/Workflows/BudgetChangeOrderWorkflowTest.php
```

### Run with Coverage
```bash
./vendor/bin/phpunit --coverage-html coverage
```

## Test Coverage

### BudgetService Tests (BudgetServiceTest.php)

#### Cost Code Assignment
- Assign cost codes to project
- Remove existing assignments not in new list
- Handle empty cost code list

#### Budget Setup
- Create new budget for project cost code
- Create budget change order when updating existing budget
- Fail when cost code not assigned
- Create decrease change order when reducing budget

#### Budget Change Orders
- Create budget change order
- Fail to create BCO for nonexistent budget

#### BCO Approval
- Approve BCO and update budget
- Cannot approve BCO in invalid status

#### Budget Validation
- Validate PO against budget with sufficient funds
- Reject PO when budget exceeded
- Reject PO when no budget exists

#### Budget Updates
- Update budget commitment
- Handle nonexistent budget when updating commitment
- Update job cost actual

#### Reporting
- Get project budget summary
- Get budget change order history

### PurchaseOrderService Tests (PurchaseOrderServiceTest.php)

#### PO Number Generation
- Generate PO number
- Generate sequential PO numbers

#### Purchase Order Creation
- Create purchase order with items
- Create PO with zero items

#### RFQ Management
- Create RFQ with items and suppliers
- Record supplier quote
- Convert RFQ to purchase order

#### Supplier Portal
- Get supplier dashboard data
- Get supplier catalog
- Add item to supplier catalog

#### Price Tracking
- Track item price changes
- Get price history for item
- Get price comparison across suppliers

### ApprovalService Tests (ApprovalServiceTest.php)

#### Submit for Approval
- Auto-approve when no workflow exists
- Submit entity for approval with single-level workflow
- Submit entity for approval with multi-level workflow
- Use project-specific workflow over company workflow
- Resolve approvers from project roles

#### Process Approval
- Process single-level approval
- Advance to next level in multi-level approval
- Process rejection
- Prevent unauthorized user from approving
- Prevent approving non-pending request

#### Queries
- Get pending approvals for user
- Get approval history for entity

### PoChangeOrderService Tests (PoChangeOrderServiceTest.php)

#### Create PO Change Order
- Create PCO with amount increase
- Create PCO with amount decrease
- Fail to create PCO for nonexistent PO
- Create PCO with details array

#### Approve PO Change Order
- Approve PCO and update PO total
- Store original total on first approval
- Cannot approve PCO in invalid status
- Handle zero amount change order

#### History
- Get PO change order history
- Return empty history for PO without change orders

#### Validation
- Validate PCO increase against budget
- Reject PCO when budget insufficient
- Validate PCO decrease without budget check
- Fail validation for nonexistent PO

#### Integration
- Full create and approve workflow
- Handle multiple PCOs accumulation

## Feature Workflow Tests

### PurchaseOrderBudgetWorkflowTest
Tests the complete workflow from budget setup through PO creation, commitment, and receiving:
1. Assign cost code to project
2. Setup project budget
3. Validate PO against budget
4. Create purchase order
5. Update budget commitment
6. Verify budget summary
7. Receive order (partial)
8. Update budget actuals
9. Create second PO and verify tracking
10. Final budget verification

### ApprovalWorkflowTest
Tests multi-level approval workflows:
1. Single-level approval workflow
2. Multi-level approval workflow
3. Rejection at level one
4. Role-based approvers
5. Unauthorized approval prevention
6. Pending approvals query

### BudgetChangeOrderWorkflowTest
Tests the complete BCO workflow:
1. BCO increase workflow with approval
2. BCO decrease workflow reduces budget
3. Multiple BCOs accumulate correctly
4. Rejected BCO does not affect budget
5. BCO auto-approved when no workflow exists

## Key Testing Principles

1. **Isolation**: Each test runs with a fresh database (RefreshDatabase trait)
2. **Context**: Company context is set via session for multi-tenancy testing
3. **Authentication**: Tests use actingAs() to simulate authenticated users
4. **Fakes**: Queue and Notification fakes prevent side effects
5. **Transactions**: Service-level transactions are tested for rollback behavior

## Adding New Tests

When adding new tests, follow these patterns:

```php
/** @test */
public function descriptive_test_name()
{
    // Arrange - Set up test data
    $data = ...;
    
    // Act - Execute the code being tested
    $result = $this->service->method($data);
    
    // Assert - Verify the results
    $this->assertTrue($result['success']);
    $this->assertDatabaseHas('table', [...]);
}
```

## Continuous Integration

These tests are designed to run in CI environments:
- Database is migrated fresh for each test
- No external dependencies (email, queues are faked)
- Fast execution with in-memory SQLite (configure in phpunit.xml)

## Maintenance

When modifying core services:
1. Run the full test suite before committing
2. Add tests for new functionality
3. Update existing tests if behavior changes
4. Ensure multi-tenancy isolation is maintained
