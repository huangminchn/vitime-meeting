<?php
require_once SERVICE_DIR.'users/SysAdmin.php';
require_once SERVICE_DIR.'UserSession.php';
/**
 * 系统管理类
 * 主要工作：
 * 1. 管理系统
 * 2. 管理企业用户
 *  
 * @author gray.liu
 * @email gaoomei@gmail.com
 * @date 2012-4-20
 */
class AdminManage {
	
	/**
	 * @var CI_Controller
	 */
	public $CI;

	/**
	 * 系统管理员
	 * @var SysAdmin
	 */
	protected $admin_user;
	
	/**
	 * 实例
	 * @var AdminManage
	 */
	private static $_instance;
	
	public function __construct(SysAdmin $user = NULL){
		$this->admin_user = $user;
		$this->CI = &get_instance();
	}
	
	public static function getInstance(SysAdmin $user = NULL){
		if(self::$_instance instanceof AdminManage){
			return self::$_instance;
		}else{
			return self::$_instance = new AdminManage($user);
		}
	}
	
	public function getUser(){
		if(!empty($this->admin_user)){
			return $this->admin_user;
		}else{
			return $this->admin_user = UserSession::getUser();
		}
	}
	
	/**
	 * 登录
	 * @param array $postData
	 */
	public function login($postData){
		$username = $postData['username'];
		$password = $postData['password'];
		
		//加载数据库访问模型
		$this->CI->load->model('admin/Admin_model');
		$user = $this->CI->Admin_model->getUserByName($username);
		
		if(empty($user)){
			return "用户不存在";
		}
		
		if(make_password($username, $password) != $user['password']){
			return "用户名或密码不对";
		}
		$this->admin_user = new SysAdmin($user); 
		UserSession::setUser($this->admin_user);
		return true;
	}
	
	/**
	 * 注册
	 * @param array $postData
	 */
	public function register($postData){
		
	}
	
	/**
	 * 登出
	 */
	public function logout(){
		$this->admin_user = null;
		UserSession::setUser(null);
	}
	
	/**
	 * 读取一个公司
	 * @param int $user_id
	 * @param int $cmp_id
	 * @return array
	 */
	public function getCompany($user_id,$cmp_id){
		$this->CI->load->model('company/Company_user_model','CompanyUserModel');
		$cmp_admin = $this->CI->CompanyUserModel->getUserById($user_id,$cmp_id);
		if(!empty($cmp_admin)){
			$this->CI->load->model('company/Company_model','CompanyModel');
			$company = $this->CI->CompanyModel->get($cmp_id);
			unset($company['id']);
			$cmp_admin = array_merge($cmp_admin,$company);
		}
		return $cmp_admin;
	}
	
	/**
	 * 新增企业管理员
	 * @param CmpAdmin $user
	 */
	public function addCmpAdmin(CmpAdmin $user){}
	
	/**
	 * 更新企业管理员
	 * @param CmpAdmin $user
	 */
	public function updateCmpAdmin($postData){
		$user_id = $postData['user_id'];
		$company_id = $postData['company_id'];
		$username = $postData['username'];
		$password = $postData['password'];
		$mobile = $postData['mobile'];
		$email = $postData['email'];
		$companyName = $postData['company_name'];
		$companyMark = $postData['company_mark'];
		$status = $postData['status'];
		
		//加载数据库访问模型
		$this->CI->load->model('company/Company_model','CompanyModel');
		$this->CI->load->model('company/Company_user_model','CompanyUserModel');
		$cmpAdmin = $this->CI->CompanyUserModel->getUserById($user_id,$company_id);
		if(empty($cmpAdmin)){
			return "参数错误";
		}
		
		$company = $this->CI->CompanyModel->getCompanyByMark($companyMark);
		if(!empty($company) && $company['id'] != $company_id){
			return "{$companyMark} 企业标识已经存在，不能使用";
		}
		
		$user = $this->CI->CompanyUserModel->getUserByName($username);
		if(!empty($user) && $user['id'] != $cmpAdmin['id']){
			return "该用户名已经被注册";
		}
		
		
		//开启事务
		$this->CI->db->trans_begin();
		if($company['company_name'] != $companyName || $company['company_mark'] != $companyMark){
			$company['company_name'] = $companyName;
			$company['company_mark'] = $companyMark;
			unset($company['id']);
			$where = array('id'=>$company_id);
			$rs = $this->CI->CompanyModel->update($company,$where);
			if($rs != 1){
				$this->CI->db->trans_rollback();
				return "更新企业资料失败";
			}
		}
		
		
		$admin = new CmpAdmin();
		$admin->username = $username;
		if(!empty($password)){
			$admin->password = make_password($username, $password);
		}
		$admin->mobile = $mobile;
		$admin->email = $email;
		$admin->status = intval($status);

		$where = array('id'=>$user_id,'company_id'=>$company_id);
		$rs = $this->CI->CompanyUserModel->update($admin->toArray(),$where);
		if($rs == 1){
			$this->CI->db->trans_commit();
			return true;
		}else{
			$this->CI->db->trans_rollback();
			return "更新管理员资料失败";
		}
	}
	
	/**
	 * 删除企业管理员
	 * @param CmpAdmin $user
	 */
	public function deleteCmpAdmin(CmpAdmin $user){}
	
	/**
	 * 企业管理员列表
	 * @param int $page
	 * @param int $limit
	 */
	public function listCmpAdmin($page = 1,$limit = 10){
		$this->CI->load->model('company/Company_user_model','CompanyUserModel');
		$userlist = $this->CI->CompanyUserModel->getCmpAdminList($page,$limit);
		return $userlist;
	}
	
}

?>