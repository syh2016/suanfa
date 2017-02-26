<?php 
header("Content-type:text/html;charset=utf-8");
main();//启动程序 
/**
* 程序主函数 
* @author   syh
* @param    int $hang 表格行数
* @param    int $lie  表格列数
* @return   null
*/
function main($hang=10,$lie=10)
{
	$data=get_rnd_data($hang,$lie);//获取随机数据
	$points=get_points($data); //点坐标
	$group=point_group($points);//陆地集合
	echo get_table($data,$group);//输出表格
 	echo '陆地总数为'.count($group);
}

/**
* 判断两点是否相邻
* @author   syh 
* @param    string  $point1='1,2' 点横纵坐标
* @param    string  $point2='2,3'
* @return   bool   
*/
function is_near($point1,$point2)
{
	$point1=explode(',',$point1);
	$point2=explode(',',$point2);
	// 两点纵坐标相同
	$if1=($point1[0]-$point2[0]===0);
	// 两点横坐标相同
	$if2=($point1[1]-$point2[1]===0);
	// 两点横坐标相邻
	$if3=(abs($point1[1]-$point2[1])===1);
	// 两点纵坐标相邻
	$if4=(abs($point1[0]-$point2[0])===1);
	$list=[$if1&&$if3,$if2&&$if4,$if3&&$if4];
	return in_array('true', $list);
}

/**
* 判断点与集合中的点是否相邻 
* @author   syh
* @param    string $point '4,5'
* @param    array  $arr 点集合['1,2','3,4']
* @return   bool
*/
// 判断当前点是否与集合中的点相邻
function is_near_arr($point,$arr)
{
	foreach ($arr as $key => $value)
	{
		if (is_near($point,$value))
		{
			return true;
		}
	}
	return false;
}

 
/**
* 生成随机数据 
* @author   syh
* @param    int  $hang 行数
* @param    int  $lie  列数
* @return   array  二维数组
*/
function get_rnd_data($hang,$lie)
{
	// 生成单行随机数
	$rnd_one=function($num){
		$count=0;
		$list=[];//存放值为1的key
		while ($count < $num)
		{
			$return[$count+1] = mt_rand(0,1); 
			$count = count($return);
		}
		return $return;	
	};

	for ($i=1; $i <=$lie ; $i++)
	{ 
		$list[$i]=$rnd_one($hang);
	}
	return $list;
}

/**
* 生成表格与数据 
* @author   syh
* @param    array $data 二维数组
* @return   array 数据与表格
*/
// 生成表格与坐标
function get_table($data,$group)
{
	
	$bgcolor_list=get_bgcolor(count($group));
	$bgcolor=function ($str)use($group,$bgcolor_list){
		foreach ($group as $key => $arr)
		{	 
			if (in_array($str,$arr))
			{
				return $bgcolor_list[$key];
			}
		}	 
		return ' ';
	};

	$str="<center><table border='1'>";
	foreach ($data as $y => $arr)
	{
		$str.="<tr height='20'>";
		foreach ($arr as $x=> $v)
		{
			if ($v===1)
			{
				$str.="<td bgcolor=".$bgcolor($x.','.$y)." width='20'>";
				$str.=1;	 
			}
			else
			{
				$str.="<td width='20'>";	 	 
			}

			$str.='</td>';
		}
		$str.='</tr>';

	}
	$str.='</table></center>';
  	return $str;
}


/**
* 生成表格坐标
* @author   syh
* @param    array $data 二维数组
* @return   array 二维点坐标
*/
// 生成坐标
function get_points($data)
{
	
	$points=[];//保存坐标
	foreach ($data as $y => $arr)
	{
		foreach ($arr as $x=> $v)
		{
			if ($v===1)
			{
				$points[]=$x.','.$y;
			} 
		}
	 
	}
	return $points;
}

/**
 * 计算集合（陆地）总数 
 * @author   syh
 * @param    array $points 坐标集合 ['1,2','2,3']
 * @return   array
 */ 
// 计算陆地总数
function point_group($points)
{ 
	$list_all=[];//保存点集合
	while(count($points)>0)
	{
		$list=[array_shift($points)];
		// 此处为重点！！！！
		// 每次找到相邻点后必须从头开始遍历
		// ['1,2','1,3','2,4','1,4']
		// 4个点相连，如果不重新遍历，就会跳过第三个点
		n100:
		foreach ($points as $key => $value)
		{
			if (is_near_arr($value,$list))
			{
				array_push($list,$value);
				unset($points[$key]);
				goto n100;
			}
		}

		$list_all[]=$list;	
	}
	return $list_all;
}
/**
* 随机生成颜色 
* @author   syh
* @param    int $num 颜色种类
* @return   array 颜色集合
*/
function get_bgcolor($num)
{
	$list=[];//保存所有颜色
	$count=count($list);
	while ($count < $num)
	{
		$str='';
	 	for ($i=0; $i <6; $i++)
	 	{ 
	 		// 生成16进制颜色
	 		$str.=dechex(mt_rand(1,15));
	 	}
	 	if (!in_array($str,$list))
	 	{
			$list[]=$str;
	 	}
	 	$count=count($list);
	}

	return $list;
}
?>