<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div><h2>插件设置</h2><br>
    <form method="post" action="options.php">
        <?php
            settings_fields( 'nm_setting_group' );
            $setting = nm_get_setting();
        ?>
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row"><label>使用方法</label></th>
                <td><p>方法1：</p>
                    <p>新建一个页面：文本框输入 <code>[nm][/nm]</code> 即可</p>
                    <p>方法2：</p>
                    <p>新建一个模板，使用下面的函数：</p>
                    <p>添加 <code>&lt;?php netease_music();?&gt;</code> 到需要的位置</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label>显示设置</label></th>
                <td>
                    <ul class="nm-color-ul">
                        <?php $color = array(
                            array(
                                'title' => '帐号ID',
                                'key' => 'id',
                                'default' => '30829298'
                            ),
                            array(
                                'title' => '每行显示专辑数量',
                                'key' => 'number',
                                'default' => '4'
                            ),
                            array(
                                'title' => '每页显示专辑数量',
                                'key' => 'perpage',
                                'default' => '12'
                            ),
                        );
                            foreach ($color as $key => $V) {
                                ?>
                                <li class="nm-color-li">
                                    <code><?php echo $V['title'];?></code>
                                    <?php $color = $setting[$V['key']] ? $setting[$V['key']] : $V['default'];?>
                                    <input name="<?php echo nm_setting_key($V['key']);?>" type="text" value="<?php echo $color;?>" id="nm-default-color" class="regular-text nm-color-picker" />
                                </li>
                            <?php }
                        ?>
                    </ul>
                    <p class="description">点击你的个人主页，URL类似为<code>http://music.163.com/#/user/home?id=30829298</code>，<code>30829298</code>就是你的ID</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="url">音乐页面地址</label></th>
                <td>
                    <select name="<?php echo nm_setting_key('pagename');?>" id="pagename">
                        <?php $config_name = nm_get_setting("pagename");$pages = get_pages(array('post_type' => 'page','post_status' => 'publish'));
                            foreach($pages as $val){
                                $selected = ($val->post_name == $config_name)? 'selected="selected"' : "";
                                $page_title = $val->post_title;
                                $page_name = $val->post_name;
                                echo "<option class='level-0' value='{$page_name}' {$selected}>{$page_title}</option>";
                            }
                        ?>
                    </select>
                    <strong> （分页需要，必须选择！）</strong>
                </td>
            </tr>
			<tr valign="top">
                <th scope="row"><label for="<?php echo nm_setting_key('small');?>">小缩略图</label></th>
                <td>
                    <label for="<?php echo nm_setting_key('small');?>">
                        <input type="checkbox" name="<?php echo nm_setting_key('small');?>" id="small" value="1" <?php if(nm_get_setting("small")) echo 'checked="checked"';?>>
                    </label>
					<p class="description">如果你觉得默认专辑封面过大可以勾选此选项</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="<?php echo nm_setting_key('tworow');?>">歌曲列表双列</label></th>
                <td>
                    <label for="<?php echo nm_setting_key('tworow');?>">
                        <input type="checkbox" name="<?php echo nm_setting_key('tworow');?>" id="tworow" value="1" <?php if(nm_get_setting("tworow")) echo 'checked="checked"';?>>
                    </label>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="nm-submit-form">
            <input type="submit" class="button-primary muhermit_submit_form_btn" name="save" value="<?php _e('Save Changes') ?>"/>
        </div>
    </form>
    <style>
        .nm-color-li{position: relative;padding-left: 120px}
        .nm-color-li code{position: absolute;left: 0;top: 1px;}
    </style>
</div>