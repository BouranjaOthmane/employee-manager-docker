<div class="row">
    <div class="col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-folder-open"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Documents</span>
                <span class="info-box-number">{{ $employee->documents->count() }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-plane-departure"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Vacations</span>
                <span class="info-box-number">{{ $employee->vacations->count() }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Salary records</span>
                <span class="info-box-number">{{ $employee->salaries->count() }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-secondary"><i class="fas fa-user-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Status</span>
                <span class="info-box-number text-capitalize">{{ $employee->status }}</span>
            </div>
        </div>
    </div>
</div>

<div class="callout callout-info">
    <h5 class="mb-2">Quick summary</h5>
    <p class="mb-0 text-muted">
        Here you can manage employee documents, vacations, and salary history from one place.
        Use the tabs above to navigate.
    </p>
</div>
