<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="image-upload-modal" data-keyboard="false" data-image-id="{{ $imageId }}" data-field-id="{{ $fieldId }}">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-dark text-white">
				<h5 class="modal-title">{{ __('Upload an Image') }}</h5>
			</div>
			<div class="modal-body">
				<input type="hidden" id="image-blob" value="" />
				<input type="file" class="d-none" id="uploaded-image" accept="image/*" />
				<div class="drop" id="dropbox">
					<p class="text-center d-none d-md-block">{{ __('Drag and Drop Image Here') }}</p>
					<div class="text-center">
						<button class="btn btn-dark" id="open-file-picker">{{ __('Open File Picker') }}</button>
					</div>
				</div>
				<div class="d-none" id="image-preview"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="save-uploaded-image" disabled>{{ __('Save') }}</button>
        		<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
			</div>
		</div>
	</div>
</div>