<?php $this->load->view('/company/cmp_admin_nav.php')?>
<div class="reserBox">
    	<div class="col">
            <h3>安排会议</h3>
            <div class="colBox">
                <form id="reserForm" name="reserForm" method='post' action='/company/do_company_reservation' onsubmit="return do_open_meeting(this)">
                <input type='hidden' name='user_list'>
                    <ul>
                        <li>
                            <div><span class="redStar">*</span>会议主题 <span class="stip">最多输入50个字</span></div>
                            <input type="text" name="title" class="inputStyle" value="" maxlength='50' value='<?php echo $title?>'/>
                        </li>
                        <li>
                            <div>开始时间</div>
                            <input name='start_time' id='start_time' class='year' value='<?php echo $start_time?>' />
                            <select name="hour" class="month" >
                               <?php for($m = 00;$m<=23;$m++):?>
                            	<option value='<?php echo $m;?>' <?php echo $hour == $m?'selected':'';?>><?php echo str_pad($m,2, '0',STR_PAD_LEFT);?></option>
                            	<?php endfor;?>
                            </select>
                            
                            	
                            <select name="minutes" class="day">
                            	<?php $maxM = 59; for($M = 0;$M<=$maxM;$M +=5):?>
                            	<option value='<?php echo $M;?>' <?php echo $minutes == $M?'selected':'';?>><?php echo str_pad($M,2, '0',STR_PAD_LEFT);?></option>
                            <?php endfor;?>
                            </select>
                        </li>
                        <li>
                            <div>会议时长(分钟)</div>
                             <input name='time_length' class='year' value='<?php echo $time_length?>' />
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
    	<?php if(!empty($errMsg)):?>
    	<ul>
    		<li>
             	<div class="errorTip">
                 	<span class="icon icon-error"></span><?php echo $errMsg?>
             	</div>
        	</li>
    	</ul>
    	<?php endif;?>
    <div class="reserBox">
        <div class="col left">
            <h3>用户列表</h3>
            <div class="colBox">
                <div class="userlist" id='left_user_list'>
                <?php foreach($user_list as $user):?>
                	<span><input type="checkbox" value='<?php echo $user['id']?>'/><label><?php echo (!empty($user['name'])?$user['name']:$user['username']);?></label></span>
                <?php endforeach;?>    
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <div class="col right">
            <h3>已选用户</h3>
            <div class="colBox" >
                <div class="userlist" id='right_user_list'>
                
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="reserBtn">
        <button type="submit" class="btn btnBlue" onclick='do_open_meeting(document.forms.reserForm)'>保存</button>
        <button type="button" class="btn btnRed" onclick='window.location.href="/company/company_meeting"'>取消</button>
    </div>
