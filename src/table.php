<?php
/**
* 
*/
class color 
{
	// 二维数组记录表个内容
	public static $table;


	private static $cur_row = 0;
	private static $row_with = array();
	public static $TAB_SLICE = ' | ';

	public static $str;
	

	public static function table()
	{
		self::$cur_row = 0;
		self::$table = array();
		return new self;
	}


	public function td($str , $align = 'left' ,$type='')
	{
		//$type 分为几种 '':普通单元格; 'span':多列占位格 ;'br':行间隔符
		// 单元格对齐
		switch ($align) {
			case 'right':
				$align = STR_PAD_LEFT;
				break;
			case 'center':
				$align = STR_PAD_BOTH;
				break;
			case 'left':
			default:
				$align = STR_PAD_RIGHT;
				break;
		}
		self::$table[self::$cur_row][] = array('str'=>$str,'align' => $align ,'type'=>$type);

		$col = sizeof(self::$table[self::$cur_row]) -1;

		// 记录这列的最宽值
		$td_longth = mb_strlen($str);//中文还有问题...

		self::$row_with[$col] = isset(self::$row_with[$col])&&(self::$row_with[$col]>$td_longth)?self::$row_with[$col]:$td_longth;

		for ($i= self::$cur_row; $i >= 0 ; $i--) { 

			if (!isset(self::$table[$i][$col]) || (self::$table[$i][$col]['type'] == 'br')) {
				continue;
			}
			self::$table[$i][$col]['str'] = str_pad(self::$table[$i][$col]['str'], self::$row_with[$col],' ', self::$table[$i][$col]['align']);
		}
		
		return $this;
	}

	public function br($br_str = ''){
		if($br_str != ''){
			$this->br();
			// 如果设定了每行间隔字符,那么就想法显示一下
			$this->td($br_str,null,'br')->br();
		}
		self::$cur_row++;
		return $this;
	}

	public function __call($method , $args)
	{

		if (strpos($method ,'td') === 0 ) {
			$col_span = trim($method , 'td');
			$col_span += 0;
			$args[1] = isset($args[1])?$args[1]:null;			
			for ($i=1; $i < $col_span; $i++) { 
				$this->td('' ,'right','span');//类型为多列单元格
			}
			$this->td($args[0] , $args[1] );//注意考虑下单元格类型

			return $this;
		}
	}

	public function __toString()
	{
		$str = '';
// var_dump(self::$table);
		// 合并单元格.∑
		foreach (self::$table as $row) {
			$col_buffer = '';// 单元格合并缓冲区
			foreach ($row as $td) {
				//判断是否为待合并单元格
				if ( $td['type'] == 'span') {
					$col_buffer .= $td['str'].str_repeat(' ', mb_strlen(self::$TAB_SLICE) );
				}elseif ($td['type'] == 'br') {
					$pad_longth = array_sum(self::$row_with) + mb_strlen(self::$TAB_SLICE) * count(self::$row_with) -1;//饿了,为啥多一个?
					$str .= str_pad('', $pad_longth , $td['str']);
				}elseif (!empty($col_buffer)) {
					$length = mb_strlen($col_buffer) + mb_strlen($td['str']);
					$str .= (str_pad($td['str'] , $length , ' ',  $td['align'] ).self::$TAB_SLICE);
					$col_buffer = '';//用完就清空
				}else{
					$str .= $td['str'].self::$TAB_SLICE;				
				}
			}

			$str .= "\n";
		}
		return $str;
	}


}



$str = color::table()
			->td4('title','center')->br('-')
			->td('row1')->td2('Centertitle','center')->td('kjdfkajdksfjkld')->br()
			->td('rewwwwww')->td('chinese','center')->td('Hello World')->td('')->br()
			->td('a')->td('b','center')->td('c')->td('d')->br()
			->td3('b3','center')->td('c')->br();


echo $str;
