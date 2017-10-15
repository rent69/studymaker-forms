	jQuery(document).ready(function($){

			function reloadCaptcha(){ $("#captchax").attr("src","php/captcha/captcha.php?r=" + Math.random()); }
			$('.captcode').click(function(e){
				e.preventDefault();
				reloadCaptcha();
			});
			
			function swapButton(){
				var txtswap = $(".form-footer #submit-all");
				if (txtswap.text() == txtswap.data("btntext-sending")) {
					txtswap.text(txtswap.data("btntext-original"));
				} else {
					txtswap.data("btntext-original", txtswap.text());
					txtswap.text(txtswap.data("btntext-sending"));
				}
			}

			var usingFallback = true,
				smartForm = $('#smart-form'),
				smartSubmitButton = $('#submit-all'),
				dzTitleCliker = $('.dz-drop-title,.dz-clicker'),
				dzNoAttachment = "Please attach some files",
				dzAcceptedFiles = ".jpg,.jpeg,.pjpeg,.png,.gif,.bmp,.tiff",
				dzErrorMsg = $("<div class='dz-attachment-er'><p></p></div>"),
				dzFileTooBig = "<div class='dz-attachment-er'><p>File too big upload 2MB or less</p></div>",
				dzFilesExceeded = "<div class='dz-attachment-er'><p>Attach a maximum of 3 files </p></div>",
				dzFileInvalid = "<div class='dz-attachment-er'><p>File not supported, upload images only </p></div>",
				responseDiv = $('.result'),
				smartFormOptions = { };
				
				smartForm.ajaxForm(smartFormOptions);
				Dropzone.autoDiscover = false;
				
				var myDropzone = new Dropzone("#myDropzone",{ 
					url: 'php/smartprocess.php',
					autoProcessQueue: false,
					uploadMultiple: true,
					parallelUploads: 3,
					maxFiles: 3,
					maxFilesize: 5,
					addRemoveLinks: true,					
					dictRemoveFile: 'x',
					dictCancelUpload:'x',
					dictDefaultMessage: '',
					dictMaxFilesExceeded: dzFilesExceeded,
					dictInvalidFileType: dzFileInvalid,
					dictFileTooBig: dzFileTooBig,
					acceptedFiles: dzAcceptedFiles,
					init: function() {
						
						usingFallback = false;
						var myDropzone = this;
						if( usingFallback == true ){
							dzTitleCliker.hide();	
						} else {
							dzTitleCliker.show();
						}
						
						smartSubmitButton.on('click', function (e) {
							e.preventDefault();
							e.stopPropagation();
							if (myDropzone.files.length == 0){
								$("#myDropzone").addClass('dz-attachment-n').removeClass('dz-attachment-y');
								errorMsgs = dzNoAttachment;
								$("#myDropzone").after(dzErrorMsg);
								dzErrorMsg.find('p').html(errorMsgs);
							}						
								
							if (smartForm.valid()) {
								if (myDropzone.getQueuedFiles().length > 0) {
									myDropzone.processQueue();
								}
								else {
									smartForm.ajaxSubmit(smartFormOptions);
								}
							}
						});
						
						this.on("addedfile", function (file) {
							$("#myDropzone").addClass('dz-attachment-y').removeClass('dz-attachment-n');
							smartForm.find(dzErrorMsg).remove();
						});
						
						
						this.on("removedfile", function(file) {
							$("#myDropzone").addClass('dz-attachment-y').removeClass('dz-attachment-n');
							smartForm.find('.dz-attachment-er').remove();
						});						
						
						this.on("maxfilesexceeded", function(file){
							this.removeFile(file);
							this.addFile(file);
						});
						
						
						this.on("sending", function(file, xhr, formData) {
							formData.append("sendername", jQuery("#sendername").val());
							formData.append("emailaddress", jQuery("#emailaddress").val());
							formData.append("sendersubject", jQuery("#sendersubject").val());
							formData.append("sendermessage", jQuery("#sendermessage").val());
							formData.append("captcha", jQuery("#captcha").val());
							swapButton();
						});
	
						
						this.on("error", function(file, response) {
							$("#myDropzone").addClass('dz-attachment-n').removeClass('dz-attachment-y');
							setTimeout(function(){							
								responseDiv.html(response);
								$(".dz-preview").each(function(){							 
									var previewR = 	$(this);
									if (previewR.hasClass('dz-error')) {
										$("#myDropzone").next('.dz-attachment-er').remove();
										$("#myDropzone").after(dzErrorMsg);
										dzErrorMsg.html(response);
										responseDiv.find('.dz-attachment-er').remove();
									}
								});	
							},1);
						});
						
						
						this.on("success", function(file, response) {
							swapButton();
							responseDiv.html(response).delay(7000).fadeOut();
							if( $('.alert-error').length == 0){
								$('.field').removeClass("state-error, state-success");
								myDropzone.removeAllFiles();
								smartForm.resetForm();
								reloadCaptcha();
							}							
						});						
									

						this.on("complete", function(file) {
													 
						});
							
						this.on("queuecomplete", function (file) {
														   
						});					
						
					}
				});	
				
				
			   
				smartForm.validate({
						errorClass: "state-error",
						validClass: "state-success",
						errorElement: "em",
						onkeyup: false,
						onclick: false,
						rules: {
								sendername: {
										required: true,
										minlength: 2
								},		
								emailaddress: {
										required: true,
										email: true
								},
								sendersubject: {
										required: true,
										minlength: 4
								},								
								sendermessage: {
										required: true,
										minlength: 10
								},
								captcha:{
									required:true,
									remote:'php/captcha/process.php'
								}
						},
						messages:{
								sendername: {
										required: 'Enter your name',
										minlength: 'Name must be at least 2 characters'
								},				
								emailaddress: {
										required: 'Enter your email address',
										email: 'Enter a VALID email address'
								},
								sendersubject: {
										required: 'Subject is important',
										minlength: 'Subject must be at least 4 characters'
								},														
								sendermessage: {
										required: 'Oops you forgot your message',
										minlength: 'Message must be at least 10 characters'
								},															
								captcha:{
										required: 'You must enter the captcha code',
										remote:'Captcha code is incorrect'
								}
						},
						highlight: function(element, errorClass, validClass) {
								$(element).closest('.field').addClass(errorClass).removeClass(validClass);
						},
						unhighlight: function(element, errorClass, validClass) {
								$(element).closest('.field').removeClass(errorClass).addClass(validClass);
						},
						errorPlacement: function(error, element) {
						   if (element.is(":radio") || element.is(":checkbox")) {
									element.closest('.option-group').after(error);
						   } else {
									error.insertAfter(element.parent());
						   }
						}
						
				});		
		
	});				
    