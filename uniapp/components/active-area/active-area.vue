// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

<template>
	<view :class="'active-area ' + type">
		<view class="a-header">
			<slot name="header"></slot>
		</view>
		<view class="public-class a-main">
			<slot name="main-header"></slot>
			<view v-if="type == 'hot'">
				<goods-list type="home-hot" :list="lists"></goods-list>
			</view>
			<scroll-view v-else style="white-space: nowrap;" scroll-x="true" scroll-with-animation="true">
				<navigator v-for="(item, index) in lists" :key="index"
					:style="{background: type == 'seckill' && '#F6EDEE'}" class="goods-item bg-white" hover-class="none"
					:url="'/pages/goods_details/goods_details?id=' + item.id">
					<custom-image width="240rpx" height="240rpx" radius="10rpx" lazy-load :src="item.image">
					</custom-image>
					<view style="padding: 8rpx">
						<view class="goods-name line1">{{item.name}}</view>
						<view class="row wrap">
							<price-format class="mr10" :weight="500" :price="item.seckill_price || item.price" :first-size="32"
								:second-size="22" :subscript-size="22" color="#FF2C3C"></price-format>
							<price-format :price="item.min_price || item.market_price" color="#999999" :second-size="22"
								:first-size="22" :subscript-size="22" :line-through="true"></price-format>
						</view>
					</view>
				</navigator>
			</scroll-view>
		</view>
	</view>
</template>

<script>
	import {
		trottle,
		getRect
	} from "@/utils/tools";

	export default {
		data() {
			return {
				currentSwiper: 0,
			};
		},

		components: {},
		props: {
			type: {
				type: String,
				default: ''
			},
			lists: {
				type: Array,
				default: () => []
			}
		},
		watch: {
			"lists": function(value) {
				if (!value) {
					return;
				}
			}
		},

		beforeMount: function() {

		},
		destroyed: function() {},
		methods: {}
	};
</script>
<style lang="scss" scoped>
	.active-area {
		background-repeat: no-repeat;
		background-size: 100% auto;
		border-radius: 10rpx;

		&.seckill {
			background-image: url(../../static/images/home_seckill_bg.png);
		}

		.a-main {
			position: relative;
			box-sizing: border-box;

			.goods-item {
				width: 240rpx;
				margin-left: 16rpx;
				display: inline-block;
				border-radius: 10rpx;
				overflow: hidden;

				&:first-of-type {
					margin-left: 20rpx;
				}

				&:last-of-type {
					margin-right: 20rpx;
				}
			}
		}
	}
</style>
