<?php
/**
 * 
 */
?>
<div id="frm-msg" class="frm-msg"></div>
<div class="frm_desk">
    <div class="frm_desk_title" style="
       <?php if ($status === 'rejected'):?>color:red
       <?php elseif ($status === 'pending'): ?>color:blue
       <?php elseif ($status === 'approved'): ?>color:green
       <?php endif; ?>
       ">
      <?php echo $title ?>
    </div>
    <?php if ($status !== 'completed'): ?>
    <div class="frm_desk_content"><?php echo $content ?></div>
    <div class="frm_desk_file" id="frm_desk_file">
      <?php if (($status ?? 'none') === 'none'): ?><span>Download → Sign → Upload → Submit</span>
      <?php elseif ($status === 'pending'): ?><span>Your document is under review. You can upload again.</span>
      <?php elseif ($status === 'rejected'): ?><span>Download form → Fill out again → Upload → Submit</span>
      <?php elseif ($status === 'approved'): ?><span>Pending setup → Active</span>
      <?php else: ?>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php if ($status == 'completed'): ?>
    <div>
      <div class="frm_desk_select_opt">
          <div>View and edit your profile</div>
          <a href="/user"  class="frm-btn btn-select">Go to</a>
      </div>
      <div class="frm_desk_select_opt" style="margin-top:20px">
        <a>View your appointments</a>
        <a href="/buddy-calendar" class="frm-btn btn-select">Go to</a>
      </div>
      <div class="frm_desk_select_opt" style="margin-top:20px">
          <a>Set available dates and times</a>
          <a href="/buddy-schedule" class="frm-btn btn-select">Go to</a>
      </div>
    </div>
    <?php elseif ($status == 'approved'): ?>
    <div class="frm_desk_step">
      <a href="/user"><button type="submit" class="frm-btn btn-select" name="frm_desk_url">View Profile</button></a>
      <a href="/my-tickets"><button type="submit" class="frm-btn btn-select" name="frm_desk_url">Send Ticket</button></a>
      <a href="/"><button type="submit" class="frm-btn btn-select" name="frm_desk_url">Back Home</button></a>
    </div>
    <?php else: ?>
    <div id="pdf-upload">
        <div class="frm_desk_step">
          <button type="submit" class="frm-btn btn-select">
            <a href="<?php echo $file_url ?>" download="">
            Download PDF
            </a>
          </button>
          <button type="submit" class="frm-btn btn-select" id="frm_desk_upload">Choose file</button>
          <input type="file" id="frm_file_input" name="file" accept="application/pdf" style="display: none;">
          <button type="submit" class="frm-btn btn-select" id="frm_desk_submit">Submit</button>
        </div>
  </div>
  <?php endif; ?>
</div>