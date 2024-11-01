		jQuery( document ).ready(function($) {
			$('input[type=radio][name=hear_about_us]').change(function() {
				var value = this.value;
				if (value == 'Private') {
					$('#ctviwoo_ust_wrapper').hide(300);
				}
				else if (this.value == 'Company') {
					$('#ctviwoo_ust_wrapper').show(300);
				}
			});	
			
			if ($('input[name="hear_about_us"]:checked').val() == undefined) {
				$("input[name=hear_about_us][value='Private']").prop('checked', true);
				$('#ctviwoo_ust_wrapper').hide(300);
			} else if ($('input[name="hear_about_us"]:checked').val() == "Company") {
				$('#ctviwoo_ust_wrapper').show(300);
			} else {
				$('#ctviwoo_ust_wrapper').hide(300);
			}
		});