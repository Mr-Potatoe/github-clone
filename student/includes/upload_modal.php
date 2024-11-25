


            <!-- Upload Modal -->
            <div class="modal fade" id="upload-modal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadModalLabel">Upload New Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="student.php" method="post" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="project_name" class="form-label">Project Name</label>
                                    <input type="text" name="project_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="project_members" class="form-label">Project Members</label>
                                    <textarea name="project_members" class="form-control" rows="2" placeholder="Enter member names, separated by commas" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="external_link" class="form-label">Project Link</label>
                                    <input type="url" name="external_link" class="form-control" placeholder="https://example.com">
                                </div>
                                <div class="mb-3">
                                    <label for="project_photo" class="form-label">Project Photo</label>
                                    <input type="file" name="project_photo" class="form-control" accept="image/*" required>
                                </div>
                                <div class="mb-3">
                                    <label for="project_file" class="form-label">Project File (ZIP only)</label>
                                    <input type="file" name="project_file" class="form-control" accept=".zip" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-cloud-upload"></i> Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>