<div id="frm-confirm-overlay" class="frm-overlay">
    <div class="frm-confirm-box" style="min-height:100px">
       <div id="frm-confirm-box" style="width:100%">
          <p>Are you sure?</p>
          <div>
            <textarea id="frm-confirm-text" placeholder="Please enter reject reason..."></textarea>
          </div>
          <div class="frm-confirm-actions">
            <button id="frm-confirm-yes" class="confirm-button button-primary">Yes</button>
            <button id="frm-confirm-no" class="confirm-button button">Cancel</button>
          </div>
        </div>
    </div>
</div>
<div id="frm-msg" class="frm-msg"></div>
<div class="app-review-box">
    <h1>Application Review</h1>
    <div class="app-review-tools">
        <div style="display:inline-block; margin-right:10px;">
            <input type="text"
                   name="search_email"
                   placeholder="Search email..."
                   value="">
            <button class="button" name="list_opt">Search</button>
        </div>
        <div style="display:inline-block;">
            <select name="status">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
            <button class="button" name="list_opt">Filter</button>
        </div>
    </div>
    <div class="app-review-list">
        <table class="widefat striped fixed">
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th>Email</th>
                    <th width="100px">Status</th>
                    <th width="100px">File</th>
                    <th>Last Notes</th>
                    <th>Created At</th>
                    <th>Opt</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo esc_html($row->id); ?></td>
                        <td><?php echo esc_html($row->email); ?></td>
                        <td>
                            <?php
                                if ($row->status == 'pending') {
                                    echo '<span style="color:orange;">Pending</span>';
                                } elseif ($row->status == 'approved') {
                                    echo '<span style="color:green;">Approved</span>';
                                } elseif ($row->status == 'rejected') {
                                    echo '<span style="color:red;">Rejected</span>';
                                }
                            ?>
                        </td>
        
                        <td>
                            <?php if (!empty($row->file_url)): ?>
                                <a href="<?php echo esc_url($row->file_url); ?>" target="_blank">View</a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($row->last_notes); ?></td>
                        <td><?php echo esc_html($row->created_at); ?></td>
                        <td>
                            <?php
                                if ($row->status == 'pending') {
                                    echo '<button class="button button-primary frm-approve" data-id="'.$row->id.'">
                                        Approve
                                    </button>
                                
                                    <button style="margin-left:5px" class="button button-secondary frm-reject" data-id="'.$row->id.'">
                                        Reject
                                    </button>';
                                }else{
                                    echo '<button class="button button-secondary frm-reset" data-id="'.$row->id.'">
                                        Reset
                                    </button>';
                                }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="app-review-pagination">
    <?php if ($total_pages >= 1): ?>
    <div class="tablenav">
        <div class="tablenav-pages">
            <?php if ($current_page > 1): ?>
                <a class="button" data-page="<?php echo $current_page - 1; ?>" name="list_opt>«</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if ($i == $current_page): ?>
                <span class="button button-primary"><?php echo $i; ?></span>
            <?php else: ?>
                <a class="button" name="list_opt" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endif; ?>
            <?php endfor; ?>
            <?php if ($current_page < $total_pages): ?>
            <a class="button" data-page="<?php echo $current_page + 1; ?>" name="list_opt">»</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    </div>    
</div>