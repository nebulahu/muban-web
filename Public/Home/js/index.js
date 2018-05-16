var weilefu = {
	pageInit : function(){
		weilefu.indexNeswTab();
	},
	footerInter : function(navindex){
		var i = navindex+1;
		$(".module_foot").find('a:eq('+navindex+')').find(".pic").attr("src","images/foot0"+i+".png").next(".text").css({"color":"#da2222"})
	},
	moduleAboutBall : function(BallLinkIndex){
		mTouch('.module_fixed_ball').on('tap', function (e) {
		  $(".module_main_ball").show("fast");
		  $(".module_ball_mask").show();
		});
		mTouch('.module_ball_mask').on('tap', function (e) {
		  $(this).hide();
		  $(".module_main_ball").hide();
		});
		mTouch('.ball_close').on('tap', function (e) {
		  $(".module_main_ball").hide();
		  $(".module_ball_mask").hide();
		});
		$(".module_main_ball").find(".link").eq(BallLinkIndex).css({"border-color":"#fff"})
	},
	collectionTab : function(){

		mTouch('.icon_gift').on('tap', function (e) {

		  $(".gitfInfoBox").remove();

		  if ($(this).hasClass("mark")) {

		  	var infoBox = "<div class='gitfInfoBox mark'>"+"已取消收藏"+"</div>" ;
		  	
		  	$(this).removeClass("mark");

		  	$(this).attr("src","images/icon_gift01.png");

		  }else{

		  	var infoBox = "<div class='gitfInfoBox mark'>"+"已收藏"+"</div>" ;

		  	$(this).addClass("mark");

		  	$(this).attr("src","images/icon_gift1.png");

		  }

		  $("body").append(infoBox);

		  setTimeout(function(){
		  	$(".gitfInfoBox").hide("fast");
		  },500);

		});
	},
	indexNeswTab : function() {
		mTouch('#indexTab li').on('tap', function (e) {
		  var i = $('#indexTab li').index($(this));
		  $('#indexTab li').eq(i).addClass('active').siblings().removeClass('active');
		  $('#indexTabMain .main').eq(i).show().siblings().hide();
		});
	},
	/*购物车飞入*/
	shoppingCart : function(){
		mTouch('.shopping_cart').on('tap', function (e) {
		  $(".shopping_cart_box").remove();

		  var i = $(this).siblings(".link_pic").find(".pic").attr("src");

		  var boxHtml = "<img src="+i+" class='shopping_cart_box'>"
		  $("body").append(boxHtml);

		});
	},
	submitAjax : function(){
		$.ajax({
			type : 'POST',
			url : url_form,
			data : formIdObj.serialize(),
			dataType : 'json',
			success : function(data){
				if(data.status){
					
					
				}else{
					
				}
			}
		});
	},
	shopingNav : function(navIndex){
		$(".shoping_nav").find(".link").eq(navIndex).addClass("active");
	},
	purchaseList : function(){
		
		/*计算 总价----数量*/
		function totalPrice (){
			var sum = 0;
			$(".chose_want").each(function(){
				/*单价*/
				var a = parseFloat($(this).parent("li").find(".list_price").text());
				
				/*数量*/
				var s = $(this).parent("li").find(".show_num").val();

				var all = s*a;

				sum += all;

			})
			$("#totalNum").text($(".chose_want").length);
			
			$("#price_mun").text(sum.toFixed(2));
		}

		totalPrice();

		/*单品数量加减*/
		function addAndReduce (){
			
			mTouch('.chose_number .reduce').on('tap', function (e) {

			  	var mun = $(this).siblings(".show_num").val();
			  	if (mun>1) {
			  		mun--;
			  		$(this).siblings(".show_num").val(mun);
			  		totalPrice ()
			  	}

			});
			mTouch('.chose_number .add').on('tap', function (e) {

			  	var mun = $(this).siblings(".show_num").val();
			  	if (mun<99) {
			  		mun++;
			  		$(this).siblings(".show_num").val(mun);
			  		totalPrice ()
			  	}
			  	
			});
		}
		addAndReduce ();

		/*勾选商品*/
		mTouch('.purchase_list .left').on('tap', function (e) {
		  if ($(this).hasClass("chose_want")) {

		  	$(this).removeClass("chose_want");

		  	sum = 0;

		  	totalPrice()

		  }else{
		  	$(this).addClass("chose_want");

		  	sum = 0;
		  	totalPrice()
		  	
		  }

		});

		/*滑动删除*/
		mTouch('.purchase_list li').on('swipeleft', function (e) {
		  //console.log("1")
		  $(this).find(".delete_this").css({"right":0,"opacity":"1"});
		  $(this).find(".chose_number").css({"margin-right":".3rem"});
		}).on('swiperight', function (e) {
		  $('.purchase_list li').find(".delete_this").css({"right":"-.5rem","opacity":"0"});
		  $('.purchase_list li').find(".chose_number").css({"margin-right":"0"});
		});
		mTouch('.delete_this').on('tap', function (e) {
		  $(this).parent("li").remove();
		  totalPrice ();
		});

		/*输入框--购买数量*/
		$(".show_num").blur(function(){
			if ($(this).val()>99) {
				$(this).val("99")
			}else if ($(this).val()<1) {
				$(this).val("1");
			};
			totalPrice();
		})

		/*结算*/
		mTouch('#submitForm').on('tap', function (e) {

		  layer.confirm('是否确认下单', {
		      btn: ['再看看','是的'] //按钮
		    }, function(){
		      //layer.msg('的确很重要', {icon: 1});
		      layer.closeAll()
		    }, function(){
		      
		      /*==*/
		      $.ajax({
				type : 'POST',
				url : url_form,
				data : formIdObj.serialize(),
				dataType : 'json',
				success : function(data){
					if(data.status){
					  layer.msg('下单成功', {icon: 1});
					  var arry = [];
					  $(".chose_want").each(function(){
					  	var i = parseInt($(this).parent("li").attr("id"));
					  	arry.push(i)
					  	

					  })
					  var Final = arry.join(",");

					  $("#depositInp").val(Final);
					}else{
						
					}
				}
			  });
		      /*==*/
		    });
		})
	},
	/*评价评分*/
	evaluationScore : function(){
		/*评论板显示隐藏*/
		mTouch('#shwoEvaluateBox').on('tap', function (e) {
		  $(".review_board").show("fast");
		  $(".review_board_mask").show();
		});
		mTouch('.review_board_mask').on('tap', function (e) {
		  $(".review_board").hide();
		  $(".review_board_mask").hide();
		});
		/*评价分数*/
		
		mTouch('.fraction_chose .lis_fra_chose').on('tap', function (e) {
		  var i = $(this).index();

		  $(this).parents("li").val(i+1)
		  $(this).parent(".fraction_chose").find(".lis_fra_chose").each(function(){
		  	if ($(this).index()>i) {
		  		$(this).css({"background":"#d1d1d1"});
		  	}else{
		  		$(this).css({"background":"#f8a700"});
		  	}
		  })
		});
		/*楼层插入*/
		mTouch('#subFormVal').on('tap', function (e) {

		  var content = $("#evaluateInp").val();

		  var bulidFloor = "<li>"+"<div class='top clr'>"+"<a href='javascript:void(0);' class='link_pic'>"+"<img src='images/indexNav5.png' alt='' class='pic'>"+"</a>"+"<div class='name'>狗***3</div>"+"</div>"+"<div class='cen'>"+content+"</div>"+"<div class='time'>2016-11-14</div>"+"</li>";

		  if (content.length<1 || content.length>199) {
		  	layer.msg('请控制在1~199个字符内');
		  }else{

		  	$.ajax({
				type : 'POST',
				url : url_form,
				data : formIdObj.serialize(),
				dataType : 'json',
				success : function(data){
					if(data.status){
						
					  	$("#evaluationBuilding").prepend(bulidFloor);
					  	$(".review_board").hide();
					  	$(".review_board_mask").hide();
					  	$("#evaluateInp").val("");
					  	$(".review_board").find("li").val("5");
					  	$(".lis_fra_chose").css({"background":"#f8a700"})
					  	layer.msg('评论成功');
						if ($(towList)) {
							$("#evaluationBuilding li:last").remove();
						};
					}else{
						
					}
				}
			});

		  }
		});
	},
	/*累计评分*/
	cumulativeScore : function(){
		$(".cumulative_fraction").each(function(){
			var i = Math.ceil($(this).attr("data"));

			$(this).find(".lis").each(function(){
				if ($(this).index()>=i) {
			  		$(this).css({"background":"#d1d1d1"});
			  	}else{
			  		$(this).css({"background":"#f8a700"});
			  	}
			})
		})
	}
}