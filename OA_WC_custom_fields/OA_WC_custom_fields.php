<?php
/*
Plugin Name: WooCommerce Custom Fields On Checkout - Campos asociados a la emisión de Facturas/Boletas en su tienda
Plugin URI: https://oscaralderete.com/
Description: Agrega un selector de Factura/Boleta a su tienda y otro de DNI/RUC y sus contrapartes en la Zona Admin
Version: 1.0.0
Author: Oscar Alderete
Author URI: https://oscaralderete.com/
*/
if(!defined('ABSPATH')){exit;}

define('OA_WC_CUSTOM_FIELDS_VERSION',1);

if(in_array('woocommerce/woocommerce.php',apply_filters('active_plugins',get_option('active_plugins')))){
	//Add custom fields
	add_filter('woocommerce_checkout_fields',function($fields){
		//Custom fields
		$r=[
			'Boleta'=>__('Boleta','woocommerce'),
			'Factura'=>__('Factura','woocommerce'),
		];
		$fields['billing']['OA_WC_tipo_comprobante']=[
			'label'=>__('Tipo de comprobante de pago','woocommerce'),
			'type'=>'select',
			'required'=>true, 
			'class'=>array('form-row-wide','address-field'),
			'clear'=>true,
			'options'=>$r
		];
		$fields['billing']['OA_WC_dni']=[
			'label'=>__('Número de documento de identidad','woocommerce'),
			'type'=>'text',
			'required'=>true,
			'class'=>array('form-row-wide','address-field','OA_WC_boleta'),
			'clear'=>true
		];
		$fields['billing']['OA_WC_razon_social']=[
			'label'=>__('Razón social de su empresa','woocommerce'),
			'type'=>'text',
			'required'=>true,
			'class'=>array('form-row-wide','address-field','OA_WC_hidden','OA_WC_factura'),
			'clear'=>true,
			'value'=>'NN'
		];
		$fields['billing']['OA_WC_ruc']=[
			'label'=>__('Número de RUC','woocommerce'),
			'type'=>'text',
			'required'=>true,
			'class'=>array('form-row-wide','address-field','OA_WC_hidden','OA_WC_factura'),
			'clear'=>true,
			'value'=>'00000000000'
		];

		//Override fields
		$fields['billing']['billing_country']=[
			'label'=>__('País','woocommerce'),
			'type'=>'text',
			'required'=>true,
			'class'=>array('form-row-wide','address-field','OA_WC_hidden'),
			'clear'=>true,
			'value'=>'PE',
			'priority'=>40
		];
		$fields['billing']['billing_state']=[
			'label'=>__('Departamento','woocommerce'),
			'type'=>'text',
			'required'=>true, 
			'class'=>array('form-row-wide','address-field'),
			'clear'=>true,
			'value'=>'LIMA',
			'priority'=>50
		];
		$fields['billing']['billing_city']=[
			'label'=>__('Provincia','woocommerce'),
			'type'=>'text',
			'required'=>true, 
			'class'=>array('form-row-wide','address-field'),
			'clear'=>true,
			'value'=>'LIMA',
			'priority'=>60
		];
		$fields['billing']['billing_postcode']=[
			'label'=>__('Distrito','woocommerce'),
			'type'=>'text',
			'required'=>true, 
			'class'=>array('form-row-wide','address-field'),
			'clear'=>true,
			'priority'=>70
		];
		$fields['shipping']['shipping_country']=[
			'label'=>__('País','woocommerce'),
			'type'=>'text',
			'required'=>true,
			'class'=>array('form-row-wide','address-field','OA_WC_hidden'),
			'clear'=>true,
			'value'=>'PE',
			'priority'=>40
		];
		$fields['shipping']['shipping_state']=[
			'label'=>__('Departamento','woocommerce'),
			'type'=>'text',
			'required'=>true, 
			'class'=>array('form-row-wide','address-field'),
			'clear'=>true,
			'value'=>'LIMA',
			'priority'=>50
		];
		$fields['shipping']['shipping_city']=[
			'label'=>__('Provincia','woocommerce'),
			'type'=>'text',
			'required'=>true, 
			'class'=>array('form-row-wide','address-field'),
			'clear'=>true,
			'value'=>'LIMA',
			'priority'=>60
		];
		$fields['shipping']['shipping_postcode']=[
			'label'=>__('Distrito','woocommerce'),
			'type'=>'text',
			'required'=>true, 
			'class'=>array('form-row-wide','address-field'),
			'clear'=>true,
			'priority'=>70
		];

		//Remove fields
		unset($fields['billing']['billing_company']);
		unset($fields['billing']['billing_address_2']);
		unset($fields['shipping']['shipping_company']);
		unset($fields['shipping']['shipping_address_2']);

		//Custom ordering
		$inputs_order=array(
			'billing_first_name', 
			'billing_last_name',
			'billing_address_1', 
			'billing_country',
			'billing_state',
			'billing_city',
			'billing_postcode',
			'OA_WC_tipo_comprobante',
			'OA_WC_dni',
			'OA_WC_razon_social',
			'OA_WC_ruc',
			'billing_email',
			'billing_phone'
		);
		$reordered=[];
		foreach($inputs_order as $i){
			$reordered[$i]=$fields['billing'][$i];
		}
		$fields['billing']=$reordered;
		$inputs_order=array(
			'shipping_first_name', 
			'shipping_last_name',
			'shipping_address_1', 
			'shipping_country',
			'shipping_state',
			'shipping_city',
			'shipping_postcode'
		);
		$reordered=[];
		foreach($inputs_order as $i){
			$reordered[$i]=$fields['shipping'][$i];
		}
		$fields['shipping']=$reordered;
		
		return $fields;
	});

	//Format on admin
	add_filter('woocommerce_localisation_address_formats',function($formats){
		$formats['PE'] = "{name}\n{company}\n{address_2}\n{address_1}\n{state} {city} {postcode}\n{country}";
	
		return $formats;
	});

	//Custom styles + JS events
	add_filter('wp_footer',function(){
		//Only on checkout page
		if(!is_checkout()&&!is_wc_endpoint_url()){
			return;
		}
		$arrayDepartamentos=['AMAZONAS'=>'AMAZONAS','ANCASH'=>'ANCASH','APURIMAC'=>'APURIMAC','AREQUIPA'=>'AREQUIPA','AYACUCHO'=>'AYACUCHO','CAJAMARCA'=>'CAJAMARCA','CALLAO'=>'CALLAO','CUSCO'=>'CUSCO','HUANCAVELICA'=>'HUANCAVELICA','HUANUCO'=>'HUANUCO','ICA'=>'ICA','JUNIN'=>'JUNIN','LA LIBERTAD'=>'LA LIBERTAD','LAMBAYEQUE'=>'LAMBAYEQUE','LIMA'=>'LIMA','LORETO'=>'LORETO','MADRE DE DIOS'=>'MADRE DE DIOS','MOQUEGUA'=>'MOQUEGUA','PASCO'=>'PASCO','PIURA'=>'PIURA','PUNO'=>'PUNO','SAN MARTIN'=>'SAN MARTIN','TACNA'=>'TACNA','TUMBES'=>'TUMBES','UCAYALI'=>'UCAYALI'];
		$arrayProvincias=['BARRANCA'=>'BARRANCA','CAJATAMBO'=>'CAJATAMBO','CAÑETE'=>'CAÑETE','CANTA'=>'CANTA','HUARAL'=>'HUARAL','HUAROCHIRI'=>'HUAROCHIRI','HUAURA'=>'HUAURA','LIMA'=>'LIMA','OYON'=>'OYON','YAUYOS'=>'YAUYOS'];
		$arrayDistritos=['ANCON'=>'ANCON','ATE'=>'ATE','BARRANCO'=>'BARRANCO','BREÑA'=>'BREÑA','CARABAYLLO'=>'CARABAYLLO','CHACLACAYO'=>'CHACLACAYO','CHORRILLOS'=>'CHORRILLOS','CIENEGUILLA'=>'CIENEGUILLA','COMAS'=>'COMAS','EL AGUSTINO'=>'EL AGUSTINO','INDEPENDENCIA'=>'INDEPENDENCIA','JESUS MARIA'=>'JESUS MARIA','LA MOLINA'=>'LA MOLINA','LA VICTORIA'=>'LA VICTORIA','LIMA'=>'LIMA','LINCE'=>'LINCE','LOS OLIVOS'=>'LOS OLIVOS','LURIGANCHO'=>'LURIGANCHO','LURIN'=>'LURIN','MAGDALENA DEL MAR'=>'MAGDALENA DEL MAR','MIRAFLORES'=>'MIRAFLORES','PACHACAMAC'=>'PACHACAMAC','PUCUSANA'=>'PUCUSANA','PUEBLO LIBRE'=>'PUEBLO LIBRE','PUENTE PIEDRA'=>'PUENTE PIEDRA','PUNTA HERMOSA'=>'PUNTA HERMOSA','PUNTA NEGRA'=>'PUNTA NEGRA','RIMAC'=>'RIMAC','SAN BARTOLO'=>'SAN BARTOLO','SAN BORJA'=>'SAN BORJA','SAN ISIDRO'=>'SAN ISIDRO','SAN JUAN DE LURIGANCHO'=>'SAN JUAN DE LURIGANCHO','SAN JUAN DE MIRAFLORES'=>'SAN JUAN DE MIRAFLORES','SAN LUIS'=>'SAN LUIS','SAN MARTIN DE PORRES'=>'SAN MARTIN DE PORRES','SAN MIGUEL'=>'SAN MIGUEL','SANTA ANITA'=>'SANTA ANITA','SANTA MARIA DEL MAR'=>'SANTA MARIA DEL MAR','SANTA ROSA'=>'SANTA ROSA','SANTIAGO DE SURCO'=>'SANTIAGO DE SURCO','SURQUILLO'=>'SURQUILLO','VILLA EL SALVADOR'=>'VILLA EL SALVADOR','VILLA MARIA DEL TRIUNFO'=>'VILLA MARIA DEL TRIUNFO'];

		?>
		<!-- Developed by Oscar Alderete - me@oscaralderete.com -->
		<div id="div_billing_state" class="OA_WC_div OA_WC_hidden">
			<ul>
				<?php
				foreach($arrayDepartamentos as $k=>$v){
				?>
				<li><a onclick="OA_WC_custom_fields.assignState(this)" data-value="<?php echo $k; ?>"><?php echo $v; ?></a></li>
				<?php
				}
				?>
			</ul>
		</div>
		<div id="div_billing_city" class="OA_WC_div OA_WC_hidden">
			<ul></ul>
		</div>
		<div id="div_billing_postcode" class="OA_WC_div OA_WC_hidden">
			<ul></ul>
		</div>

		<link type="text/css" rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ); ?>css/styles.css?v=<?php echo OA_WC_CUSTOM_FIELDS_VERSION ?>">
		<script src="<?php echo plugin_dir_url( __FILE__ ); ?>js/scripts.js?v=<?php echo OA_WC_CUSTOM_FIELDS_VERSION ?>"></script>
		<?php
	});

	//Save custom fields
	add_action('woocommerce_checkout_update_order_meta',function($order_id){
		if(!empty($_POST['OA_WC_tipo_comprobante'])){
			update_post_meta($order_id,'_OA_WC_custom_fields_tc',sanitize_text_field($_POST['OA_WC_tipo_comprobante']));
		}
		if(!empty($_POST['OA_WC_dni'])){
			update_post_meta($order_id,'_OA_WC_custom_fields_dni',sanitize_text_field($_POST['OA_WC_dni']));
		}
		if(!empty($_POST['OA_WC_razon_social'])){
			update_post_meta($order_id,'_OA_WC_custom_fields_rs',sanitize_text_field($_POST['OA_WC_razon_social']));
		}
		if(!empty($_POST['OA_WC_ruc'])){
			update_post_meta($order_id,'_OA_WC_custom_fields_ruc',sanitize_text_field($_POST['OA_WC_ruc']));
		}
	});

	//Display info on admin zone
	add_action('woocommerce_admin_order_data_after_billing_address',function($order){
		$tc=get_post_meta($order->id,'_OA_WC_custom_fields_tc',true);
		echo '<p><strong>'.__('Tipo de comprobante de pago').':</strong><br>'.$tc.'</p>';
		if($tc=='Boleta'){
			echo '<p><strong>'.__('Número de documento de identidad').':</strong><br>'.get_post_meta($order->id,'_OA_WC_custom_fields_dni',true).'</p>';
		}
		else{
			echo '<p><strong>'.__('Razón social').':</strong><br>'.get_post_meta($order->id,'_OA_WC_custom_fields_rs',true).'</p>';
			echo '<p><strong>'.__('RUC').':</strong><br>'.get_post_meta($order->id,'_OA_WC_custom_fields_ruc',true).'</p>';
		}
	},10,1);
}