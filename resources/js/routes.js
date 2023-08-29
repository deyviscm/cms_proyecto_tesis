import Vue from 'vue';
import VueRouter from 'vue-router';

import AuthContainer from '../components/layouts/full/AuthContainer.vue';
import MainContainer from '../components/layouts/full/MainContainer.vue';

import AuthService from '../services/AuthService';

import Login from '../components/auth/Login';
import Home from '../components/pages/Home';
import HomeClienteDash2 from '../components/pages/HomeClienteDash2';
import HomeClienteDash3 from '../components/pages/HomeClienteDash3';
import Dashboard from '../components/pages/Dashboard';
import ReporteEntidad from '../components/pages/ReporteEntidad';
import ReporteCriterio from '../components/pages/ReporteCriterio';
import ReporteEvaluacion from '../components/pages/ReporteEvaluacion';
import Usuario from '../components/pages/Usuario';
import HomeEvaluador from '../components/pages/HomeEvaluador';
import RealizarEvaluacion from '../components/pages/RealizarEvaluacion';
import OrdenesCompras from '../components/pages/OrdenesCompras';
import Categorias from '../components/pages/Categorias';
import Productos from '../components/pages/Productos';

var routes = [
	{
		path: '/',
		component: AuthContainer,
		children: [
			{
				path: '',
				component: Login,
			}
		],
		meta: {
			isLogin: true
		}
	},
	{
		path: '/',
		component: MainContainer,
		children: [
			{
				path: 'home',
				component: Home,
			},
			{
				path: 'dash2',
				component: HomeClienteDash2,
			},
			{
				path: 'dash3',
				component: HomeClienteDash3,
			},
			{
				path: 'dashboard',
				component: Dashboard,
			},
			{
				path: 'reporte-entidad',
				component: ReporteEntidad,
			},
			{
				path: 'reporte-criterio',
				component: ReporteCriterio,
			},
			{
				path: 'reporte-evaluacion',
				component: ReporteEvaluacion,
			},
			{
				path: 'usuario',
				component: Usuario,
			},
			{
				path: 'evaluaciones',
				component: HomeEvaluador,
			},
			{
				path: 'realizar-evaluacion',
				component: RealizarEvaluacion,
			},
			{
				path: 'ordenes-compras',
				component: OrdenesCompras,
			},
			{
				path: 'categorias',
				component: Categorias,
			},
			{
				path: 'productos',
				component: Productos,
			},
		],
		meta: {
			requiresAuth: true
		}
	},
	{
		path: '*',
		redirect: '/'
	},
];

Vue.use(VueRouter);

var router = new VueRouter({
	routes // short for `routes: routes`
});

router.beforeEach((to, from, next) => {
	if (to.matched.some(record => record.meta.requiresAuth)) {
		if (localStorage.getItem('jwtAuth') == null) {
			next({
				path: '/login',
				params: { nextUrl: to.fullPath }
			});
		} else {
			next();
		}
	} else if (to.matched.some(record => record.meta.isLogin)) {
		if (localStorage.getItem('jwtAuth') != null) {
			next({
				path: '/home',
				params: { nextUrl: to.fullPath }
			});
		} else {
			next();
		}
	} else {
		next();
	}

})
export default router;