<?php
    class WH2600 extends IPSModule
    {

        protected function Log($Message)
        {
            IPS_LogMessage(__CLASS__, $Message);
        }

        public function Create()
        {
            parent::Create();
            $this->RegisterPropertyBoolean('Active', False);
            $this->RegisterPropertyString('IPAddress', '192.168.1.0');
            $this->RegisterPropertyInteger("Poller", 1);
            $this->RegisterTimer("Update", $this->ReadPropertyInteger("Poller") * 1000, "WH2600_Update($this->InstanceID);");
        }

        public function ApplyChanges()
        {
            parent::ApplyChanges();
            if($this->ReadPropertyBoolean('Active'))
            {
                if ( @Sys_Ping($this->ReadPropertyString("IPAddress"), 1000) )
                {
                    $this->SetStatus(102);
                    $this->SetTimerInterval("Update", $this->ReadPropertyInteger("Poller") * 1000);
                }
                else
                {
                    $this->SetStatus(201);
                    $this->SetTimerInterval("Update", 0);
                    echo "Invalid IP-Address";
                }
            }
            else
            {
                $this->SetStatus(104);
                $this->SetTimerInterval('Update', 0);
            }
            $this->RegisterVariableFloat('inTemp','Indoor Temperature','~Temperature',1);
            $this->RegisterVariableFloat('inHumi','Indoor Humidity','~Humidity.F',2);
            $this->RegisterVariableFloat('AbsPress','Absolute Pressure','~AirPressure.F',3);
            $this->RegisterVariableFloat('RelPress','Relative Pressure','~AirPressure.F',4);
            $this->RegisterVariableFloat('outTemp','Outdoor Temperature','~Temperature',5);
            $this->RegisterVariableFloat('outHumi','Outdoor Humidity','~Humidity.F',6);
            $this->RegisterVariableFloat('windir','Wind Direction','~WindDirection.Text',7);
            $this->RegisterVariableFloat('avgwind','Wind Speed','~WindSpeed.kmh',8);
            $this->RegisterVariableFloat('gustspeed','Wind Gust','~WindSpeed.kmh',9);
            $this->RegisterVariableFloat('solarrad','Solar Radiation','',10);
            $this->RegisterVariableInteger('uv','UV','',11);
            $this->RegisterVariableInteger('uvi','UVI','~UVIndex',12);
            $this->RegisterVariableFloat('rainofhourly','Hourly Rain Rate','~Rainfall',13);
            $this->RegisterVariableFloat('rainofdaily','Daily Rain','~Rainfall',14);
            $this->RegisterVariableFloat('rainofweekly','Weekly Rain','~Rainfall',15);
            $this->RegisterVariableFloat('rainofmonthly','Monthly Rain','~Rainfall',16);
            $this->RegisterVariableFloat('rainofyearly','Yearly Rain','~Rainfall',17);
        }

        public function GetConfigurationForm()
        {
            return '
            {
                "elements":
                [
                    { "type": "CheckBox", "name": "Active", "caption": "Active"},
                    { "type": "ValidationTextBox", "name": "IPAddress", "caption": "IP-Address" },
                    { "name": "Poller", "type": "IntervalBox", "caption": "Seconds" }
                
                ],
                "status":
                [
                    { "code": 102, "icon": "active", "caption": "Interface is open" },
	                { "code": 104, "icon": "inactive", "caption": "Interface is closed" },
	                { "code": 200, "icon": "error", "caption": "Interface is an error state. Please check message log for more information." },
                    { "code": 201, "icon": "error", "caption": "Invalid IP-Address" }
                ]
            }
            ';
        }

        public function Update()
        {
            if ( !Sys_Ping($this->ReadPropertyString("IPAddress"), 1000) )
            {
                $this->SetStatus(201);
                trigger_error("Invalid IP-Address", E_USER_ERROR);
                exit;
            }
            // $url = 'http://wh2600demo.neddix.com/livedata.htm';
            $url = 'http://' . $this->ReadPropertyString("IPAddress") . '/livedata.htm';
            $html = @file_get_contents($url);
            if(empty($html))
            {
                $this->SetStatus(200);
                exit;
            }
            else
            {
                $this->SetStatus(102);
            }
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            foreach($dom->getElementsByTagName('input') as $tag)
            {
                $name = $tag->getAttribute('name');
                $value = $tag->getAttribute('value');
                switch ($name)
                {
                    case 'inTemp':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'inHumi':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'AbsPress':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'RelPress':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'outTemp':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'outHumi':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'windir':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'avgwind':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'gustspeed':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'solarrad':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'uv':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'uvi':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'rainofhourly':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'rainofdaily':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'rainofweekly':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'rainofmonthly':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                    case 'rainofyearly':
                        $this->SetValue($this->GetIDForIdent($name), $value);
                        break;
                }
            }
        }

        private function SetValue($ID, $Value)
        {
            if ( GetValue($ID) !== $Value ) { SetValue($ID, $Value); }
        }
    }