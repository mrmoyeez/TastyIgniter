<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inbox extends MX_Controller {

	public function __construct() {
		parent::__construct(); 																	//  calls the constructor
		$this->load->library('customer'); 														// load the customer library
		$this->load->model('Messages_model');													// loads messages model
	}

	public function index() {
		$this->lang->load('main/inbox');  														// loads language file
		
		if (!file_exists(APPPATH .'views/main/inbox.php')) {
			show_404();
		}
			
		if ($this->session->flashdata('alert')) {
			$data['alert'] = $this->session->flashdata('alert');  								// retrieve session flashdata variable if available
		} else {
			$data['alert'] = '';
		}

		if (!$this->customer->isLogged()) {  													// if customer is not logged in redirect to account login page
  			redirect('account/login');
		}
		
		// START of retrieving lines from language file to pass to view.
		$data['text_heading'] 			= $this->lang->line('text_heading');
		$data['text_view'] 				= $this->lang->line('text_view');
		$data['text_empty'] 			= $this->lang->line('text_empty');
		$data['column_date'] 			= $this->lang->line('column_date');
		$data['column_time'] 			= $this->lang->line('column_time');
		$data['column_subject'] 		= $this->lang->line('column_subject');
		$data['column_text'] 			= $this->lang->line('column_text');
		$data['column_action'] 			= $this->lang->line('column_action');
		$data['button_back'] 			= $this->lang->line('button_back');
		// END of retrieving lines from language file to pass to view.

		$data['back'] 					= $this->config->site_url('account');
		
		$data['messages'] = array();
		$results = $this->Messages_model->getMainInbox();							// retrieve all customer messages from getMainInbox method in Messages model
		foreach ($results as $result) {					
			$data['messages'][] = array(														// create array of customer messages to pass to view
				'date'		=> mdate('%d %M %y - %H:%i', strtotime($result['date'])),
				'subject' 	=> $result['subject'],
				'body' 		=> substr(strip_tags(html_entity_decode($result['body'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
				'edit'		=> $this->config->site_url('account/inbox/view?message_id=' . $result['message_id'])
			);
		}

		$regions = array('main/header', 'main/content_left', 'main/footer');
		$this->template->regions($regions);
		$this->template->load('main/inbox', $data);
	}

	public function view() {
		$this->lang->load('main/inbox');  														// loads language file

		if (!file_exists(APPPATH .'views/main/inbox_view.php')) {
			show_404();
		}
			
		if (!$this->customer->isLogged()) {  													// if customer is not logged in redirect to account login page
  			redirect('account/login');
		}

		if ($this->session->flashdata('alert')) {
			$data['alert'] = $this->session->flashdata('alert');  								// retrieve session flashdata variable if available
		} else {
			$data['alert'] = '';
		}

		if ($this->input->get('message_id')) {															// check if customer_id is set in uri string
			$message_id = (int)$this->input->get('message_id');
		} else {
  			redirect('account/inbox');
		}

		$result = $this->Messages_model->viewMessage($message_id);								// retrieve specific customer message based on message id to be passed to view

		// START of retrieving lines from language file to pass to view.
		$data['text_heading'] 			= $this->lang->line('text_view_heading');
		$data['column_date'] 			= $this->lang->line('column_date');
		$data['column_time'] 			= $this->lang->line('column_time');
		$data['column_subject'] 		= $this->lang->line('column_subject');
		$data['button_back'] 			= $this->lang->line('button_back');
		// END of retrieving lines from language file to pass to view.

		$data['back'] 					= $this->config->site_url('account/inbox');

		if ($result) {
			$data['error'] 			= '';
			$data['message_id'] = $result['message_id'];
			$data['date'] 		= mdate('%d %M %y - %H:%i', strtotime($result['date']));
			$data['subject'] 	= $result['subject'];
			$data['body'] 		= $result['body'];
		} else {
			$data['error'] = '<p class="error">Sorry, an error has occurred.</p>';
		}

		$regions = array('main/header', 'main/content_left', 'main/footer');
		$this->template->regions($regions);
		$this->template->load('main/inbox_view', $data);
	}
}

/* End of file inbox.php */
/* Location: ./application/controllers/main/inbox.php */