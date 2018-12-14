/*
*示例插件功能：
*光标移动到目标标签，标签移动，改变背景色；光标离开时，标签恢复原来的样式
*/
(function($)
{
	// $.fn.extend()进行对象扩展
	$.fn.extend({
		// 插件功能实现
		dwqs:function(options)
		{
			// 为插件参数设定默认值
			var defaults = 
			{
				padding:20,    //移动距离
				time:300,        //移动时间
				color:"red"     //背景颜色
			};
			// 使用$.extend()覆盖插件中的默认值
			var options = $.extend(defaults,options);
			return this.each(function()
			{
				// 将this引用的DOM元素转为JQuery对象
				var obj = $(this);
				// 鼠标经过时添加动画
				obj.mouseover(function()
				{
					obj.animate({paddingLeft:options.padding},options.time);
					obj.css("backgroundColor",options.color);
				});
				// 鼠标离开时恢复
				obj.mouseout(function()
				{
					obj.animate({paddingLeft:0},options.time);
					obj.css("backgroundColor","");
				});
			});
		}    //不要有;号  否则出错
	});
})(jQuery);