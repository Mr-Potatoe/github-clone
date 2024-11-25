            <!-- Include the Delete Modal -->
            <div class="modal fade" id="delete-modal-<?php echo $row['project_id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel-<?php echo $row['project_id']; ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel-<?php echo $row['project_id']; ?>">Confirm Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this project? This action cannot be undone.
                        </div>
                        <div class="modal-footer">
                            <form action="delete_project.php" method="POST">
                                <input type="hidden" name="delete_project_id" value="<?php echo $row['project_id']; ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>