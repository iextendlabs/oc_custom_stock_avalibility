<?php
class ControllerExtensionModuleCustomAvailability extends Controller {
	private $error = array();

	public function install() {

		$ifcolumn=$this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."stock_status` LIKE 'name'");

		if($ifcolumn->num_rows)

		$this->db->query("ALTER TABLE `".DB_PREFIX."stock_status` MODIFY `name` varchar(255) NOT NULL;");

	}

	public function index() {

		$this->install();
		
		$this->load->language('extension/module/custom_availability');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_custom_availability', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/custom_availability', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/custom_availability', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_custom_availability_status'])) {
			$data['module_custom_availability_status'] = $this->request->post['module_custom_availability_status'];
		} else {
			$data['module_custom_availability_status'] = $this->config->get('module_custom_availability_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/custom_availability', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/custom_availability')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}