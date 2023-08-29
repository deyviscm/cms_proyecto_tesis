<template>
	<div>

	<a-layout id="components-layout" style="margin-bottom:-85px">
		<a-layout-sider
			breakpoint="lg"
			collapsed-width="0"
			@collapse="onCollapse"
			@breakpoint="onBreakpoint"
			v-model="collapsed"
			theme="light" 
		>
			<div class="logo" style="text-align:center">
				<img src="/assets/images/logo/logo.png?v=1.0" style="width: 150px">
			</div>
			<a-menu theme="light" mode="inline" :default-selected-keys="[selectedLink]" :style="{ height: '100%', borderRight: 0 }">
					<a-menu-item :key="index" v-for="(sidebarLink, index) in sidebarLinks">
						<router-link :to="sidebarLink.url">
							<a-icon :type="sidebarLink.icon"></a-icon>
							<span class="nav-text">{{sidebarLink.name}}</span>
						</router-link>
					</a-menu-item>
			</a-menu>
		</a-layout-sider>
		<a-layout>
			<a-layout-header style="padding: 0">
				<a-icon
					class="trigger"
					:type="collapsed ? 'menu-unfold' : 'menu-fold'"
					@click="collapse()"
				/>

				<a-dropdown style="float: right;margin-right: 10px">
					<a class="ant-dropdown-link" @click="e => e.preventDefault()" style="color:#fff">
						Mi cuenta <a-icon type="down" />
					</a>
					<a-menu slot="overlay">
						<a-menu-item>
							<a href="javascript:;" @click="logout()">Salir</a>
						</a-menu-item>
					</a-menu>
				</a-dropdown>
			</a-layout-header>
			<a-layout-content :style="{ margin: '24px 16px 0' }">
				<!-- <div :style="{ padding: '24px', background: '#fff', minHeight: '360px' }"> -->
					<!-- <keep-alive> -->
						<router-view></router-view>
					<!-- </keep-alive> -->
				<!-- </div> -->
			</a-layout-content>
			<a-layout-footer style="textAlign: center">
				© Todos los derechos reservados.
			</a-layout-footer>
		</a-layout>
	</a-layout>
	</div>
</template>

<script>

import AxiosGlobalError from "../../../services/AxiosGlobalError";
import AuthService from '../../../services/AuthService';

export default {
	name: "MainContainer",
	components: {
	},
	methods: {
		onCollapse(collapsed, type) {
			// console.log(collapsed, type);
		},
		onBreakpoint(broken) {
			// console.log(broken);
		},
		collapse(){
			this.collapsed = !this.collapsed;
			setTimeout(function(){
				window.dispatchEvent(new Event('resize'));
			},300);
		},
		logout() {
			AuthService.logout();
			this.$router.push('/');
		},
		getMenu(){
			axios
				.get("/api/menu")
				.then((response) => {
					this.sidebarLinks = response.data.data;
					console.log(this.sidebarLinks,'this.sidebarLinks')
				})
				.catch((e) => AxiosGlobalError.error(e))
				.finally(() => {});
		}
	},
	mounted: function () {
		this.getMenu()
	},
	data() {
		return {
			selectedLink: 0,
			sidebarLinks: [],
			collapsed: false
		};
	},
};
</script>

<style>
.ant-layout, .ant-layout-footer{
	background: transparent;
}

#components-layout .logo {
	background: #24A52F;margin: 0;height: 64px;line-height: 64px;
}
#components-layout .ant-layout-header{
	background: #24A52F;
}

#components-layout .trigger {
	font-size: 18px;
	line-height: 64px;
	padding: 0 24px;
	cursor: pointer;
	transition: color 0.3s;
	color: #fff;
}

#components-layout .trigger:hover {
	color: #fff;
}

#components-layout .ant-layout-sider-zero-width-trigger{
	display: none;
	width: 0;
	height: 0;
}

</style>