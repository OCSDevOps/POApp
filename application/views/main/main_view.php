<?php $this->load->view('main/component/header_home')?>

<style>
.text-error { color: red; font-size: 12px}
</style> 
<!-- Modal -->
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="gridSystemModalLabel">Your Choice</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
			<?php echo form_open(); ?>
				<div class="form-group">
					<label class="radio-inline">
					  <input type="radio" name="usertype" id="usertype1" value="Farmer" autocomplete="off"> Farmer
					</label>
					<label class="radio-inline">
					  <input type="radio" name="usertype" id="usertype2" value="Student" autocomplete="off"> Student
					</label>
					<label class="radio-inline">
					  <input type="radio" name="usertype" id="usertype3" value="Entrepreneur" autocomplete="off"> Entrepreneur
					</label>
					<small class="text-error usertype"><?php echo form_error('usertype'); ?></small>
				</div>
				<div class="form-group">
					<!--<button type="button" class="btn btn-primary">Submit</button>-->
					<input type="submit" class="btn btn-primary" name="submit" value="Submit" />
					<input type="submit" class="btn btn-warning" name="skip" value="Skip" />
					<!--<button type="button" class="btn btn-default" data-dismiss="modal">Skip</button>-->
				</div>
			<?php echo form_close(); ?>
			</div>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal -->
  <div class="modal fade" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
        <div class="modal-body">
          <p>Some text in the modal.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  

<?php $this->load->view('main/component/footer_home'); ?>

<script type="text/javascript">
    $(window).on('load', function() {
        $('#myModal').modal('show');
    });
</script>
