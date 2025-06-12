<div class="card">
    <div class="card-body">

        <!-- Outer nav-pills -->
        <ul class="nav nav-pills bg-nav-pills nav-justified mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="#home1" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0 active" aria-selected="true" role="tab">
                    Activity Timeline
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#profile1" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0" aria-selected="false" role="tab">
                    Overview
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Activity Timeline tab, active by default -->
            <div class="tab-pane active show" id="home1" role="tabpanel">

                <!-- Inner nav-tabs -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#home" data-bs-toggle="tab" aria-expanded="true" class="nav-link active show" aria-selected="true" role="tab">
                            Activity
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#profile" data-bs-toggle="tab" aria-expanded="false" class="nav-link" aria-selected="false" role="tab">
                            Notes
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#settings" data-bs-toggle="tab" aria-expanded="false" class="nav-link" aria-selected="false" role="tab">
                            Tasks
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active show" id="home" role="tabpanel">
                        <!-- Recent Activity Timeline -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="header-title">Recent Activity</h4>
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-2-fill"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="javascript:void(0);" class="dropdown-item">Settings</a>
                                        <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-0 mb-3" data-simplebar="init" style="max-height: 315px;">
                                <div class="timeline-alt py-0">
                                    <div class="timeline-item">
                                        <i class="ri-upload-line text-bg-info timeline-icon"></i>
                                        <div class="timeline-item-info">
                                            <a href="#" class="text-info fw-bold mb-1 d-block">You sold an item</a>
                                            <small>Paul Burgess just purchased “Attex - Admin Dashboard”!</small>
                                            <p class="mb-0 pb-2">
                                                <small class="text-muted">5 minutes ago</small>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <i class="ri-rocket-line text-bg-primary timeline-icon"></i>
                                        <div class="timeline-item-info">
                                            <a href="#" class="text-primary fw-bold mb-1 d-block">Product on the Bootstrap Market</a>
                                            <small>Dave Gamache added
                                                <span class="fw-bold">Admin Dashboard</span>
                                            </small>
                                            <p class="mb-0 pb-2">
                                                <small class="text-muted">30 minutes ago</small>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <i class="ri-chat-2-line text-bg-success timeline-icon"></i>
                                        <div class="timeline-item-info">
                                            <a href="#" class="text-info fw-bold mb-1 d-block">Robert Delaney</a>
                                            <small>Send you message
                                                <span class="fw-bold">"Are you there?"</span>
                                            </small>
                                            <p class="mb-0 pb-2">
                                                <small class="text-muted">2 hours ago</small>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <i class="ri-upload-line text-bg-warning timeline-icon"></i>
                                        <div class="timeline-item-info">
                                            <a href="#" class="text-primary fw-bold mb-1 d-block">Audrey Tobey</a>
                                            <small>Uploaded a photo
                                                <span class="fw-bold">"Error.jpg"</span>
                                            </small>
                                            <p class="mb-0 pb-2">
                                                <small class="text-muted">14 hours ago</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <!-- end timeline -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="profile" role="tabpanel">
                        <!-- Notes content -->
                    </div>
                    <div class="tab-pane" id="settings" role="tabpanel">
                        <!-- Tasks content -->
                    </div>
                </div>
            </div>
            <!-- Overview tab, not active by default -->
            <div class="tab-pane" id="profile1" role="tabpanel">
                <!-- Overview content -->
            </div>
        </div>
    </div>
</div>
