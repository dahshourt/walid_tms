<!-- Edit Start Date Modal -->
<div class="modal fade" id="edit-start-date-modal-{{ $value->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editStartDateModalLabel{{ $value->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg">
            <div class="modal-header bg-primary text-white rounded-top-lg">
                <h5 class="modal-title font-weight-bold" id="editStartDateModalLabel{{ $value->id }}">
                    <i class="fas fa-calendar-alt mr-2"></i> Edit Start Date
                </h5>
                <button type="button" class="close text-white opacity-75 hover-opacity-100" data-dismiss="modal"
                    data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="update-start-date-form-ajax" data-id="{{ $value->id }}">
                <div class="modal-body p-4">
                    <div class="card bg-light border-0 mb-4 rounded-lg">
                        <div class="card-body p-3">
                            <div class="row text-center">
                                <div class="col-md-4 border-right">
                                    <small class="text-uppercase text-muted font-weight-bold d-block mb-1">Group</small>
                                    <h6 class="mb-0 text-dark font-weight-bold">{{ $value->group->title }}</h6>
                                </div>
                                <div class="col-md-4 border-right">
                                    <small class="text-uppercase text-muted font-weight-bold d-block mb-1">User</small>
                                    <h6 class="mb-0 text-dark font-weight-bold">{{ $value->user->user_name }}</h6>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-uppercase text-muted font-weight-bold d-block mb-1">Man
                                        Days</small>
                                    <h6 class="mb-0 text-primary font-weight-bold">{{ $value->man_day }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label for="start_date_{{ $value->id }}" class="font-weight-bold text-dark">
                            New Start Date <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0">
                                    <i class="far fa-calendar-alt text-muted"></i>
                                </span>
                            </div>
                            <input type="date" class="form-control border-left-0 pl-0 focus-shadow-none"
                                id="start_date_{{ $value->id }}" name="start_date"
                                value="{{ $value->start_date->format('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <small class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle mr-1"></i> End date will be automatically recalculated.
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-lg px-4 py-3">
                    <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>