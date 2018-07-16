<script type="text/javascript">
				<?php
				foreach($recordTypes['records'] as $recordType){
				echo 'function handle' . $recordType['recordType'] . "(){\n\t\t\t\t\t";
				echo 'var ' . $recordType['recordType'] . '_box = document.getElementById("check_' .  $recordType['recordType'] . '");' . "\n\t\t\t\t\t";
				echo 'if(' .  $recordType['recordType'] . "_box.checked == true){\n\t\t\t\t\t\t";
				echo "[].forEach.call(document.querySelectorAll('.". $recordType['recordType']."'), function (el) {
					el.style.display = 'inline-block';
				});\n\t\t\t\t\t";
				echo "} else {\n\t\t\t\t\t\t";
				echo "[].forEach.call(document.querySelectorAll('.". $recordType['recordType']."'), function (el) {
					el.style.display = 'none';
				});
				
			}}";
			}
			?>
			</script>