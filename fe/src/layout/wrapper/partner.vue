<template>
    <div class="wrapper">
		<div class="header-wrapper">
			<header>
               <TopDoiTac></TopDoiTac> 
			</header>
          <MenuDoiTac></MenuDoiTac>
		</div>
		<div class="page-wrapper">
			<div class="page-content">
				<router-view></router-view>
			</div>
		</div>
		<div class="overlay toggle-icon"></div>
		<a href="javaScript:;" class="back-to-top"><i
				class='bx bxs-up-arrow-alt'></i></a>
		
        <BotRocker></BotRocker>
	</div>
</template>

<script>
import "../../assets/js/bootstrap.bundle.min.js";
import "../../assets/js/jquery.min.js";
import "../../assets/plugins/simplebar/js/simplebar.min.js";
import "../../assets/plugins/metismenu/js/metisMenu.min.js";
import "../../assets/js/index.js";
import "../../assets/js/app.js";

import TopDoiTac from '../components/doitac/TopDoiTac.vue';
import MenuDoiTac from '../components/doitac/MenuDoiTac.vue';
import BotRocker from '../components/BotRocker.vue';

export default {
    name: 'PartnerLayout',
    components: {
        TopDoiTac,
        MenuDoiTac,
        BotRocker
    }
}
</script>

<style>
@import "../../assets/plugins/simplebar/css/simplebar.css";
@import "../../assets/plugins/metismenu/css/metisMenu.min.css";
@import "../../assets/css/pace.min.css";
@import "../../assets/css/bootstrap.min.css";
@import "../../assets/css/bootstrap-extended.css";
@import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap");
@import "../../assets/css/app.css";
@import "../../assets/css/icons.css";
@import "../../assets/css/header-colors.css";
</style>
