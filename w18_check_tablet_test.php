<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<style >
		#failure_expect {
			float:left;
			width:50%;
		}
		pre {text-align: left;}
		table {
			width: 700px;
			border-spacing: 0;
			font-size:14px;
		}
		table th {
			color: #fff;
			padding: 8px 15px;
			background: #258;
			background:-moz-linear-gradient(rgba(34,85,136,0.7), rgba(34,85,136,0.9) 50%);
			background:-webkit-gradient(linear, 100% 0%, 100% 50%, from(rgba(34,85,136,0.7)), to(rgba(34,85,136,0.9)));
			font-weight: bold;
			border-left:1px solid #258;
			border-top:1px solid #258;
			border-bottom:1px solid #258;
			line-height: 120%;
			text-align: center;
			text-shadow:0 -1px 0 rgba(34,85,136,0.9);
			box-shadow: 0px 1px 1px rgba(255,255,255,0.3) inset;
		}
		table th:first-child {
			border-radius: 5px 0 0 0;
		}
		table th:last-child {
			border-radius:0 5px 0 0;
			border-right:1px solid #258;
			box-shadow: 2px 2px 1px rgba(0,0,0,0.1),0px 1px 1px rgba(255,255,255,0.3) inset;
		}
		table tr td {
			padding: 8px 15px;
			border-bottom: 1px solid #84b2e0;
			border-left: 1px solid #84b2e0;
			text-align: center;
		}
		table tr td:last-child {
			border-right: 1px solid #84b2e0;
			box-shadow: 2px 2px 1px rgba(0,0,0,0.1);
		}
		table tr {
			background: #fff;
		}
		table tr:nth-child(2n+1) {
			background: #f1f6fc;
		}
		table tr:last-child td {
			box-shadow: 2px 2px 1px rgba(0,0,0,0.1);
		}
		table tr:last-child td:first-child {
			border-radius: 0 0 0 5px;
		}
		table tr:last-child td:last-child {
			border-radius: 0 0 5px 0;
		}
		table tr:hover {
			background: #bbd4ee;
			cursor:pointer;
		}
	</style>
	<title>w18チェックテスト</title>
</head>
<?php
define('EXCEL_DUBLUE_QUOTATION','""');
define('LF',"\n");

const STR_KEY_NO_KEY = 'keyが存在しない';

function make_expect_data($data) {
	$res = array();
	// LFで配列にする
	$data_array = explode(LF, $data);
	// 回す
	foreach($data_array as $d) {
		// {}がついている場合飛ばす
		if(strpos($d,'{') !== false
			|| strpos($d,'}') !== false
			|| $d === ''){
			//{}が含まれているもしくは空場合飛ばす
			continue;
		}
		// :で分割する
		$d2 = explode(':',$d);
		$key = $d2[0];
		$value = $d2[1];
		// keyを作成
		$key = trim($key, EXCEL_DUBLUE_QUOTATION);

		// ,を取り除く
		$value = str_replace(',', '', $value);
		// trimする
		$value = trim($value);
		if(substr($value ,0 ,strlen(EXCEL_DUBLUE_QUOTATION)) === EXCEL_DUBLUE_QUOTATION) {
			// ""がついている場合”をtrimしてStringにする
			$res[$key] = trim($value, EXCEL_DUBLUE_QUOTATION);
		} else if($value === 'null') {
			// nullの場合nullにする
			$res[$key] = null;
		} else if(is_numeric($value)) {
			if(strpos($value,'.') !== false) {
				// .がある場合floatにする
				$res[$key] = (float)$value;
			} else {
				// それ以外はint
				$res[$key] = (int)$value;
			}
		} else {
			// is_numericでfalseの場合エラーで終わる
			echo "エラーです";
			exit();
		}
	}
	return $res;
}

function make_option($name_list, $post_data, $post_name) {
	$select_name = "";
	if(isset($post_data[$post_name])) {
		$select_name = $post_data[$post_name];
	}
	$html = "";
	foreach ($name_list as $name=>$class_name) {
		if($select_name === $class_name) {
			$html .= '<option value="' . $class_name . '" selected >' . $name . '</option>';
		} else {
			$html .= '<option value="' . $class_name . '">' . $name . '</option>';
		}
	}
	return $html;
}

$auth_code = isset($_POST['Authkey'])? trim($_POST['Authkey']): "";
$m_id = isset($_POST['MenberId'])? trim($_POST['MenberId']): "";
?>
<body>
<div>
	<div id="input">
		<form name="in" id="in" method="post" enctype="multipart/form-data">
			実行クラス名：
			<select name="class_name" id="class_name">
				<?php echo make_option(array(
					'BT_2680 相談員ログインAPI【チェック】' => 'w18_check_tablet_login',
					'BT_2680 相談員ログインAPI【業務処理】' => 'w18_tablet_login',
					'BT_2640 支援ツールダウンロードAPI【チェック】' => 'w18_check_tablet_tooldownload',
					'BT_2630 カレンダーダウンロードAPI【チェック】' => 'w18_check_tablet_calendar',
					'BT_2630 カレンダーダウンロードAPI【業務処理】' => 'w18_tablet_calendar',
					'BT_2650 参加者情報ダウンロードAPI【チェック】' => 'w18_check_tablet_shiendownload',
					'BT_2650 参加者情報ダウンロードAPI【業務処理】' => 'w18_tablet_shiendownload',
					'BT_2660 ワーク情報アップロードAPI【チェック】' => 'w18_check_tablet_workupload',
					'BT_2660 ワーク情報アップロードAPI【業務処理】' => 'w18_tablet_workupload',
					'BT_2670 同意書情報アップロードAPI【チェック】' => 'w18_check_tablet_douiupload',
					'BT_2670 同意書情報アップロードAPI【業務処理】' => 'w18_tablet_douiupload',
				), $_POST, 'class_name') ;?>
			</select>
			<br />
			有効な認証コード:<input type="text" name="Authkey" id="Authkey" value="<?php echo $auth_code;?>"/>
			<br />
			存在する参加者ID:<input type="text" name="MenberId" id="MenberId" value="<?php echo $m_id;?>"/>
			<br />
			テストデータ指定:<input type="file" name="file_name" id="file_name">
			<br />
			pngファイル指定:<input type="file" name="png_name" id="png_name">　※ワーク情報アップロードAPI【業務処理】のみ
			<br />
			<input type="submit" value="検証する"/>
		</form>
	</div>
	<div id="test">
		<p><?php
			include_once 'kizuite.net_param.inc';
			include_once 'lib.php';
			include_once 'w18_common.inc';
			require_once getcwd() .'/w18_result_interface.php';
			include_once getcwd() .'/w18_result_list.php';
			include_once getcwd() .'/w18_result.php';
			session_start();

			$SEPARATOR = "\t";
			$RESULT_TITLE = "想定結果";
			$NO_TITLE = "No.";
			$AUTH_TITLE = "有効な認証コード";
			$AUTH_UNABLE = "無効な認証コード";
			$AUTH_UNABLE_VALUE = "999999";
			$M_ID_TITLE = "有効な参加者ID";
			$M_ID_UNABLE = "無効な参加者ID";
			$M_ID_UNABLE_VALUE = "999999999";
			$M_ID_INTEGER_OVERFLOW = "integer_overflow_m_id";
			$M_ID_INTEGER_OVERFLOW_VALUE = "9999999999";
			$PARITY_TITLE = "正常なパリティ";
			$DATA_TITLE = "正常なデータ";

			$file = null;

			if($_POST['class_name'] == "w18_tablet_workupload"){
				// pngファイル
				$tmp_png_name = "/tmp/" . "w18_tmp" . microtime() ;
				if(is_uploaded_file($_FILES["png_name"]["tmp_name"])){
					if(move_uploaded_file($_FILES["png_name"]["tmp_name"], $tmp_png_name)){
						chmod($tmp_png_name, 0644);
						$png = file_get_contents($tmp_png_name );
						unlink($tmp_png_name);

						// ファイルのエンコード
						$png_md5 = base64_encode(md5($png, true));
						$png_data = base64_encode($png);

					} else {
						echo "ファイルをアップロードできません。";
						exit();
					}
				} else {
					echo "ファイルが選択されていません。";
					exit();
				}
			}

			// 一時ファイル
			$tmp_file_name = "/tmp/" . "w18_tmp" . microtime() ;
			if(is_uploaded_file($_FILES["file_name"]["tmp_name"])){
				if(move_uploaded_file($_FILES["file_name"]["tmp_name"], $tmp_file_name)){
					chmod($tmp_file_name, 0644);
					$file = file_get_contents($tmp_file_name );
					unlink($tmp_file_name);

					// ファイルのエンコード
					$from_encoding = "";
					foreach(array('UTF-8','SJIS','EUC-JP','ASCII','JIS') as $charcode){
						if(strcmp(mb_convert_encoding($file, $charcode, $charcode),$file)==0){
							$from_encoding = $charcode;
							break;
						}
					}
					$file = mb_convert_encoding($file, "utf8", $from_encoding);
					$file = str_replace($AUTH_TITLE, $auth_code, $file);
					$file = str_replace($AUTH_UNABLE, $AUTH_UNABLE_VALUE, $file);
					$file = str_replace($M_ID_TITLE, $m_id, $file);
					$file = str_replace($M_ID_UNABLE, $M_ID_UNABLE_VALUE, $file);
					$file = str_replace($M_ID_INTEGER_OVERFLOW, $M_ID_INTEGER_OVERFLOW_VALUE, $file);
					$file = str_replace($PARITY_TITLE, $png_md5, $file);
					$file = str_replace($DATA_TITLE, $png_data, $file);


					// 行ごとで処理していく
					$csvs = explode("\r\n", $file);
					$header = array();
					$test_data_array = array();
					for($i=0; $i < count($csvs); $i++) {
						$line = $csvs[$i];
						if(empty($line)) continue;
						$tmp_array = array();
						if($i==0) {
							// ヘッダー処理
							$header = explode($SEPARATOR, $line);
							continue;
						} else {
							$lines = explode($SEPARATOR, $line);
							for($j=0; $j < count($header); $j++) {
								if($header[$j] == $RESULT_TITLE) {
									if('NULL' === trim($lines[$j])) {
										$tmp_array[$header[$j]] = null;
									}else {
										$tmp_array[$header[$j]] = make_expect_data($lines[$j]);
									}
//									$tmp = str_replace("\"","" ,$lines[$j]);
//									$tmps = explode("\n", $tmp);
//									foreach ($tmps as $tmps_line) {
//										if(strpos($tmps_line,':') !== false) {
//											$tmps_line_array = explode(":", $tmps_line);
//											if(is_numeric($tmps_line_array[1])) {
//												$tmps_line_array[1] = intval($tmps_line_array[1]);
//											}
//											$tmp_array[$header[$j]][$tmps_line_array[0]] = $tmps_line_array[1];
//										}
//									}
								} else {
									$tmp_array[$header[$j]] = $lines[$j];
								}
							}
						}
						$test_data_array[] = $tmp_array;
					}

					$target_class_name = $_POST['class_name'];

					$target_class = new $target_class_name;
					$data_header = array_slice($header ,2);

					$w18_list = new w18_result_list();
					foreach ($test_data_array as $data) {
						$w18 = new w18_result();
						$result_obj = array();
						$p = array();
						foreach ($data as $key => $value) {
							if($NO_TITLE == $key) {
								$w18->set_no($value);
							} else if($RESULT_TITLE == $key) {
								$w18->set_expect_obj($data[$RESULT_TITLE]);
							} else if("" !== $value) {
								// メソッドに渡すパラメータ修正
								// "が存在しない場合は数値
								if(strpos($value,"\"") === false){
									$tmp_num = trim($value);
									if(is_numeric($tmp_num)) {
										// .が存在する
										if(strpos($tmp_num,".") !== false) {
											$value = (float)$tmp_num;
										} else {
											$value = (int)$tmp_num;
										}
									}
									if('null' === mb_strtolower($value)) {
										$value = null;
									}
									$value = $value;
								} else {
									$value = str_replace("\"", "", $value);
								}
								$p[$key] = $value;
							}
						}

						// checkが存在する場合チェック処理
						if(strpos($target_class_name, '_check_') !== false){
							$do_header = $data_header;
							if(in_array(STR_KEY_NO_KEY,$p)) {
								$key_val = array_search(STR_KEY_NO_KEY, $p);
								$index = array_search($key_val, $data_header);
								unset($do_header[$index]);
							}
							foreach ($do_header as $dh) {
								$ret = $target_class->param_check_unique($dh, $p);
								if(!is_null($ret)) {
									$w18->set_result_obj($ret);
									break;
								}
							}
						} else {
							// 業務系
							$w18->set_result_obj($target_class->execute_unique($p));
						}
						$w18_list->set_result_obj($w18);
					}

				} else {
					echo "ファイルをアップロードできません。";
					exit();
				}
			} else {
				echo "ファイルが選択されていません。";
				exit();
			}
			?></p>
	</div>
	<div id="param">
		<table>
			<tbody>
			<tr>
				<th>実行クラス名</th>
				<th>m_id</th>
				<th>ファイル名</th>
			</tr>
			<tr>
				<td><?php echo $target_class_name; ?></td>
				<td><?php echo $m_id; ?></td>
				<td><?php echo $_FILES["file_name"]["name"]; ?></td>
			</tr>
			</tbody>

		</table>
	</div>
	<div id="summary">
		<table>
			<tbody>
			<tr>
				<th>全体</th>
				<th>成功</th>
				<th>失敗</th>
			</tr>
			<tr>
				<td><?php echo $w18_list->getCountAll(); ?></td>
				<td><?php echo $w18_list->getCountSuccess(); ?></td>
				<td><?php echo $w18_list->getCountFaliure(); ?></td>
			</tr>
			</tbody>

		</table>
	</div>
	<div id="result">
		<table>
			<tbody>
			<?php foreach ($w18_list->get_result_obj_list() as $w18obj){ ?>
			<tr>
				<th>No.</th>
				<th>結果</th>
			</tr>
			<tr>
				<td><?php echo $w18obj->get_no(); ?></td>
				<td><?php echo $w18obj->get_result()?"成功":"失敗"; ?></td>
			</tr>
				<?php if(!$w18obj->get_result()){ ?>
			<tr>
				<td colspan="2">
					<div id="failure_expect">
						<p>期待値</p>
<?php var_dump( $w18obj->get_expect_obj()); ?>
					</div>
					<div id="failure_result">
						<p>結果の値</p>
<?php var_dump( $w18obj->get_result_obj()); ?>
					</div>
				</td>
			</tr>
				<?php } ?>
			<?php } ?>



			</tbody>
		</table>
	</div>

</div>
</body>
</html>
