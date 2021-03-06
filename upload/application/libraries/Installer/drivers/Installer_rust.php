<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Game AdminPanel (АдминПанель)
 *
 * 
 *
 * @package		Game AdminPanel
 * @author		Nikita Kuznetsov (ET-NiK)
 * @copyright	Copyright (c) 2014, Nikita Kuznetsov (http://hldm.org)
 * @license		http://www.gameap.ru/license.html
 * @link		http://www.gameap.ru
 * @filesource
*/

// -----------------------------------------------------------------

/**
 * Source Installer драйвер
 *
 * Драйвер для установки игровых серверов Source
 *
 * @package		Game AdminPanel
 * @category	Drivers
 * @author		Nikita Kuznetsov (ET-NiK)
 * @sinse		0.9.5
*/

class Installer_rust extends CI_Driver {
	
	// -----------------------------------------------------------------
	
	/**
	 * Список стандартных карт
	*/
	private function _get_default_map($game_code = 'rust')
	{
		$game_code = strtolower($game_code);
		
		$default_maps = array(
						'rust' 			=> 'rust_island_2013',
						'rust_exp' 		=> 'Procedural Map',
		);
		
		return $default_maps[$game_code];
	}
	
	// -----------------------------------------------------------------
	
	/**
	 * Получает дополнительные данные сервера
	 * Возвращает массив с тремя портами:
	 * 		1. Порт для подключения
	 * 		2. Query порт
	 * 		3. Rcon порт
	 * 
	 * @param int порт для подключения
	 * @return array
	 * 
	 */
	public function get_ports($connect_port = 0)
	{
		return array($connect_port, $connect_port + 1, $connect_port + 1);
	}
	
	// -----------------------------------------------------------------

	/**
	 * Получает путь к списку карт
	 */
	public function get_maps_path($game_code = 'rust')
	{
		return '';
	}
	
	// -----------------------------------------------------------------
	
	/**
	 * Получение параметра запуска игры
	*/
	public function get_start_command($game_code = '', $os = 'linux')
	{
		switch ($this->_engine_version) {
			//////
			case 'legacy':
			default:
			
				switch(strtolower($os)) {
					case 'windows':
						$start_command = 'rust_server.exe ';
						break;
						
					default:
						$start_command = './rust_server_x32 ';
						break;
				}
				
				$start_command .= '-batchmode -hostname "Rust Server" -maxplayers {maxplayers} -port {port} -datadir "serverdata/" -oxidedir "save/oxide"';
				break;
			
			/////
			case 'experimental':
			
				switch(strtolower($os)) {
					case 'windows':
						$start_command = 'RustDedicated.exe ';
						break;
						
					default:
						$start_command = './RustDedicated ';
						break;
				}
				
				$start_command .= '-batchmode -server.hostname "{hostname}" -load -server.maxplayers {maxplayers} -server.ip {ip} -server.port {port} +rcon.ip {ip} +rcon.port {rcon_port} +rcon.password {rcon_password} +server.saveinterval {saveinterval} -autoupdate';
				break;
		}

		return $start_command;
	}
	
	// -----------------------------------------------------------------
	
	/**
	 * Получение настроек для сервера по умолчанию
	*/
	public function get_default_parameters($game_code = 'rust', $os = 'linux', $parameters = array())
	{
		if ($this->_engine_version == 'experimental') {
			(isset($parameters['hostname']) && !empty($parameters['hostname']))
				OR $parameters['hostname'] = 'Rust Server';
				
			(isset($parameters['port']) && !empty($parameters['port']))
				OR $parameters['port'] = $this->server_data['server_port'];
				
			(isset($parameters['rcon_port']) && !empty($parameters['rcon_port']))
				OR $parameters['rcon_port'] = $this->server_data['server_port']+1;
				
			(isset($parameters['rcon_password']) && !empty($parameters['rcon_password']))
				OR $parameters['rcon_password'] = random_string('alnum', 8);
				
			(isset($parameters['saveinterval']) && !empty($parameters['saveinterval']))
				OR $parameters['saveinterval'] = 300;
		}
		
		(isset($parameters['maxplayers']) && !empty($parameters['maxplayers'])) 
				OR $parameters['maxplayers'] = 50;
				
		return $parameters;
	}
	
	// -----------------------------------------------------------------
	
	/**
	 * Правка конфигурационных файлов
	*/
	public function change_config()
	{
		//~ $CI =& get_instance();
		//~ 
		//~ $file = 'serverdata/cfg/server.cfg';
		//~ $dir = get_ds_file_path($this->server_data);
		//~ 
		//~ // Костыль. Меняет права файла на 666
		//~ if(strtolower($this->_os) != 'windows') {
			//~ send_command('chmod 666 {dir}/serverdata/cfg/server.cfg', $this->server_data);
		//~ }
		//~ 
		//~ // Rcon пароль
		//~ $file_contents = change_value_on_file($file_contents, 'rcon.password', $this->server_data['rcon']);
		return true;
	}
}
