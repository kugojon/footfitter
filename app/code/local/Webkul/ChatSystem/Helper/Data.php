<?php
    class Webkul_ChatSystem_Helper_Data extends Mage_Core_Helper_Abstract {

		
		
		public function getAdminMail(){
			 $adminCollection = Mage::getModel('admin/user')->getCollection();
                foreach ($adminCollection as $admin) {
                    $email=$admin->getEmail();
                    break;
                }   
            return $email;        
		}

		/**
		 * @param void check whether admin is logged in or not
		 * @var object 
		 */
		
		public function isAdminLogin() {
			$adminStatus = Mage::getModel("chatsystem/adminstatus")->getCollection();
			foreach ($adminStatus as $admin) {
				$id= $admin->getId();
			}
			$model = Mage::getModel("chatsystem/adminstatus")->load($id);
			if($model->getStatus())
			{
				$adminSessionLifetime =(int)Mage::getStoreConfig('admin/security/session_cookie_lifetime');
				if(!$adminSessionLifetime)
				{
					$adminSessionLifetime=3600;
				}
				$lastReferesh=$model->getCreatedAt();
				$currentTime=time();
				$totalLoginTime=$currentTime-$lastReferesh;
				if($adminSessionLifetime>$totalLoginTime)
				{
					return 1;
				}
			}
			
		}

		/**
		 * @param void admin id set at system configuration
		 * @return string admin id
		 */
		
		public function getAdminId(){
			return Mage::getStoreConfig('chatsystem/chatsystem/adminuserid');
		}

		/**
		 * @param void get time set at system configuration
		 * @return int return time
		 */
		
		public function getTimeToUpdate(){
			$time=Mage::getStoreConfig('chatsystem/chatsystem/timeforupdate');
			if($time=="")
				$time=10;
			return $time;
		}

		/**
		 * @param void get text set for loader at system configuration
		 * @return string return text
		 */
		
		public function getTextToLoad(){
			return Mage::getStoreConfig('chatsystem/chatsystem/loadertext');
		}

		/**
		 * @param void get admin email to send mails from admin panel
		 * @return string return text
		 */
		
		public function getAdminEmail(){
			return Mage::getStoreConfig('chatsystem/chatsystem/adminemail');
		}

		/**
		 * @param void get admin name for mail from admin panel
		 * @return string return text
		 */
		
		public function getAdminName(){
			return Mage::getStoreConfig('chatsystem/chatsystem/adminname');
		}

		/**
		 * @param void return session lifetime
		 */
		
		public function getSessionTime(){
			return Mage::getStoreConfig('admin/security/session_cookie_lifetime');
		}

		/**
		 * @param void return client session lifetime
		 */
		
		public function getclientsession(){
			return Mage::getStoreConfig('api/config/session_timeout');
		}
    }