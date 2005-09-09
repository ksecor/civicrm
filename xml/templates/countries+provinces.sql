--
-- PostgreSQL database dump
--

SET client_encoding = 'UNICODE';
SET check_function_bodies = false;

SET SESSION AUTHORIZATION 'postgres';

--
-- TOC entry 4 (OID 2200)
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
GRANT ALL ON SCHEMA public TO PUBLIC;


SET SESSION AUTHORIZATION 'shot';

SET search_path = public, pg_catalog;

--
-- TOC entry 10 (OID 104543)
-- Name: digest(text, text); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION digest(text, text) RETURNS bytea
    AS '$libdir/pgcrypto', 'pg_digest'
    LANGUAGE c;


--
-- TOC entry 11 (OID 104544)
-- Name: digest(bytea, text); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION digest(bytea, text) RETURNS bytea
    AS '$libdir/pgcrypto', 'pg_digest'
    LANGUAGE c;


--
-- TOC entry 12 (OID 104545)
-- Name: digest_exists(text); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION digest_exists(text) RETURNS boolean
    AS '$libdir/pgcrypto', 'pg_digest_exists'
    LANGUAGE c;


--
-- TOC entry 13 (OID 104546)
-- Name: hmac(text, text, text); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION hmac(text, text, text) RETURNS bytea
    AS '$libdir/pgcrypto', 'pg_hmac'
    LANGUAGE c;


--
-- TOC entry 14 (OID 104547)
-- Name: hmac(bytea, bytea, text); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION hmac(bytea, bytea, text) RETURNS bytea
    AS '$libdir/pgcrypto', 'pg_hmac'
    LANGUAGE c;


--
-- TOC entry 15 (OID 104548)
-- Name: hmac_exists(text); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION hmac_exists(text) RETURNS boolean
    AS '$libdir/pgcrypto', 'pg_hmac_exists'
    LANGUAGE c;


--
-- TOC entry 16 (OID 104549)
-- Name: crypt(text, text); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION crypt(text, text) RETURNS text
    AS '$libdir/pgcrypto', 'pg_crypt'
    LANGUAGE c;


--
-- TOC entry 17 (OID 104550)
-- Name: gen_salt(text); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION gen_salt(text) RETURNS text
    AS '$libdir/pgcrypto', 'pg_gen_salt'
    LANGUAGE c;


--
-- TOC entry 18 (OID 104551)
-- Name: gen_salt(text, integer); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION gen_salt(text, integer) RETURNS text
    AS '$libdir/pgcrypto', 'pg_gen_salt_rounds'
    LANGUAGE c;


--
-- TOC entry 19 (OID 104552)
-- Name: encrypt(bytea, bytea, text); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION encrypt(bytea, bytea, text) RETURNS bytea
    AS '$libdir/pgcrypto', 'pg_encrypt'
    LANGUAGE c;


--
-- TOC entry 20 (OID 104553)
-- Name: decrypt(bytea, bytea, text); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION decrypt(bytea, bytea, text) RETURNS bytea
    AS '$libdir/pgcrypto', 'pg_decrypt'
    LANGUAGE c;


--
-- TOC entry 21 (OID 104554)
-- Name: encrypt_iv(bytea, bytea, bytea, text); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION encrypt_iv(bytea, bytea, bytea, text) RETURNS bytea
    AS '$libdir/pgcrypto', 'pg_encrypt_iv'
    LANGUAGE c;


--
-- TOC entry 22 (OID 104555)
-- Name: decrypt_iv(bytea, bytea, bytea, text); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION decrypt_iv(bytea, bytea, bytea, text) RETURNS bytea
    AS '$libdir/pgcrypto', 'pg_decrypt_iv'
    LANGUAGE c;


--
-- TOC entry 23 (OID 104556)
-- Name: cipher_exists(text); Type: FUNCTION; Schema: public; Owner: shot
--

CREATE FUNCTION cipher_exists(text) RETURNS boolean
    AS '$libdir/pgcrypto', 'pg_cipher_exists'
    LANGUAGE c;


--
-- TOC entry 5 (OID 143708)
-- Name: countries; Type: TABLE; Schema: public; Owner: shot
--

CREATE TABLE countries (
    id integer NOT NULL,
    name text,
    country_iso_code text
);


--
-- TOC entry 6 (OID 143717)
-- Name: provinces; Type: TABLE; Schema: public; Owner: shot
--

CREATE TABLE provinces (
    id serial NOT NULL,
    country_iso_code text,
    province_iso_code text,
    name text,
    country_id integer
);


--
-- Data for TOC entry 24 (OID 143708)
-- Name: countries; Type: TABLE DATA; Schema: public; Owner: shot
--

COPY countries (id, name, country_iso_code) FROM stdin;
1001	Afghanistan	AF
1002	Albania	AL
1003	Algeria	DZ
1004	American Samoa	AS
1005	Andorra	AD
1006	Angola	AO
1007	Anguilla	AI
1008	Antarctica	AQ
1009	Antigua and Barbuda	AG
1010	Argentina	AR
1011	Armenia	AM
1012	Aruba	AW
1013	Australia	AU
1014	Austria	AT
1015	Azerbaijan	AZ
1016	Bahrain	BH
1017	Bangladesh	BD
1018	Barbados	BB
1019	Belarus	BY
1020	Belgium	BE
1021	Belize	BZ
1022	Benin	BJ
1023	Bermuda	BM
1024	Bhutan	BT
1025	Bolivia	BO
1026	Bosnia and Herzegovina	BA
1027	Botswana	BW
1028	Bouvet Island	BV
1029	Brazil	BR
1030	British Indian Ocean Territory	IO
1031	Virgin Islands, British	VG
1032	Brunei Darussalam	BN
1033	Bulgaria	BG
1034	Burkina Faso	BF
1035	Myanmar	MM
1036	Burundi	BI
1037	Cambodia	KH
1038	Cameroon	CM
1039	Canada	CA
1040	Cape Verde	CV
1041	Cayman Islands	KY
1042	Central African Republic	CF
1043	Chad	TD
1044	Chile	CL
1045	China	CN
1046	Christmas Island	CX
1047	Cocos (Keeling) Islands	CC
1048	Colombia	CO
1049	Comoros	KM
1050	Congo, The Democratic Republic of the	CD
1051	Congo	CG
1052	Cook Islands	CK
1053	Costa Rica	CR
1054	Côte d'Ivoire	CI
1055	Croatia	HR
1056	Cuba	CU
1057	Cyprus	CY
1058	Czech Republic	CZ
1059	Denmark	DK
1060	Djibouti	DJ
1061	Dominica	DM
1062	Dominican Republic	DO
1063	Timor-Leste	TL
1064	Ecuador	EC
1065	Egypt	EG
1066	El Salvador	SV
1067	Equatorial Guinea	GQ
1068	Eritrea	ER
1069	Estonia	EE
1070	Ethiopia	ET
1072	Falkland Islands (Malvinas)	FK
1073	Faroe Islands	FO
1074	Fiji	FJ
1075	Finland	FI
1076	France	FR
1077	French Guiana	GF
1078	French Polynesia	PF
1079	French Southern Territories	TF
1080	Gabon	GA
1081	Georgia	GE
1082	Germany	DE
1083	Ghana	GH
1084	Gibraltar	GI
1085	Greece	GR
1086	Greenland	GL
1087	Grenada	GD
1088	Guadeloupe	GP
1089	Guam	GU
1090	Guatemala	GT
1091	Guinea	GN
1092	Guinea-Bissau	GW
1093	Guyana	GY
1094	Haiti	HT
1095	Heard Island and McDonald Islands	HM
1096	Holy See (Vatican City State)	VA
1097	Honduras	HN
1098	Hong Kong	HK
1099	Hungary	HU
1100	Iceland	IS
1101	India	IN
1102	Indonesia	ID
1103	Iran, Islamic Republic of	IR
1104	Iraq	IQ
1105	Ireland	IE
1106	Israel	IL
1107	Italy	IT
1108	Jamaica	JM
1109	Japan	JP
1110	Jordan	JO
1111	Kazakhstan	KZ
1112	Kenya	KE
1113	Kiribati	KI
1114	Korea, Democratic People's Republic of	KP
1115	Korea, Republic of	KR
1116	Kuwait	KW
1117	Kyrgyzstan	KG
1118	Lao People's Democratic Republic	LA
1119	Latvia	LV
1120	Lebanon	LB
1121	Lesotho	LS
1122	Liberia	LR
1123	Libyan Arab Jamahiriya	LY
1124	Liechtenstein	LI
1125	Lithuania	LT
1126	Luxembourg	LU
1127	Macao	MO
1128	Macedonia, Republic of	MK
1129	Madagascar	MG
1130	Malawi	MW
1131	Malaysia	MY
1132	Maldives	MV
1133	Mali	ML
1134	Malta	MT
1135	Marshall Islands	MH
1136	Martinique	MQ
1137	Mauritania	MR
1138	Mauritius	MU
1139	Mayotte	YT
1140	Mexico	MX
1141	Micronesia, Federated States of	FM
1142	Moldova, Republic of	MD
1143	Monaco	MC
1144	Mongolia	MN
1145	Montserrat	MS
1146	Morocco	MA
1147	Mozambique	MZ
1148	Namibia	NA
1149	Nauru	NR
1150	Nepal	NP
1151	Netherlands Antilles	AN
1152	Netherlands	NL
1153	New Caledonia	NC
1154	New Zealand	NZ
1155	Nicaragua	NI
1156	Niger	NE
1157	Nigeria	NG
1158	Niue	NU
1159	Norfolk Island	NF
1160	Northern Mariana Islands	MP
1161	Norway	NO
1162	Oman	OM
1163	Pakistan	PK
1164	Palau	PW
1165	Palestinian Territory, Occupied	PS
1166	Panama	PA
1167	Papua New Guinea	PG
1168	Paraguay	PY
1169	Peru	PE
1170	Philippines	PH
1171	Pitcairn	PN
1172	Poland	PL
1173	Portugal	PT
1174	Puerto Rico	PR
1175	Qatar	QA
1176	Romania	RO
1177	Russian Federation	RU
1178	Rwanda	RW
1179	Reunion	RE
1180	Saint Helena	SH
1181	Saint Kitts and Nevis	KN
1182	Saint Lucia	LC
1183	Saint Pierre and Miquelon	PM
1184	Saint Vincent and the Grenadines	VC
1185	Samoa	WS
1186	San Marino	SM
1187	Saudi Arabia	SA
1188	Senegal	SN
1189	Seychelles	SC
1190	Sierra Leone	SL
1191	Singapore	SG
1192	Slovakia	SK
1193	Slovenia	SI
1194	Solomon Islands	SB
1195	Somalia	SO
1196	South Africa	ZA
1197	South Georgia and the South Sandwich Islands	GS
1198	Spain	ES
1199	Sri Lanka	LK
1200	Sudan	SD
1201	Suriname	SR
1202	Svalbard and Jan Mayen	SJ
1203	Swaziland	SZ
1204	Sweden	SE
1205	Switzerland	CH
1206	Syrian Arab Republic	SY
1207	Sao Tome and Principe	ST
1208	Taiwan	TW
1209	Tajikistan	TJ
1210	Tanzania, United Republic of	TZ
1211	Thailand	TH
1212	Bahamas	BS
1213	Gambia	GM
1214	Togo	TG
1215	Tokelau	TK
1216	Tonga	TO
1217	Trinidad and Tobago	TT
1218	Tunisia	TN
1219	Turkey	TR
1220	Turkmenistan	TM
1221	Turks and Caicos Islands	TC
1222	Tuvalu	TV
1223	Uganda	UG
1224	Ukraine	UA
1225	United Arab Emirates	AE
1226	United Kingdom	GB
1227	United States Minor Outlying Islands	UM
1228	United States	US
1229	Uruguay	UY
1230	Uzbekistan	UZ
1231	Vanuatu	VU
1232	Venezuela	VE
1233	Viet Nam	VN
1234	Virgin Islands, U.S.	VI
1235	Wallis and Futuna	WF
1236	Western Sahara	EH
1237	Yemen	YE
1238	Serbia and Montenegro	CS
1239	Zambia	ZM
1240	Zimbabwe	ZW
1241	Åland Islands	AX
\.


--
-- Data for TOC entry 25 (OID 143717)
-- Name: provinces; Type: TABLE DATA; Schema: public; Owner: shot
--

COPY provinces (id, country_iso_code, province_iso_code, name, country_id) FROM stdin;
3849	AE	AE-AZ	Abu Zaby	1225
3850	AE	AE-AJ	'Ajman	1225
3851	AE	AE-FU	Al Fujayrah	1225
3852	AE	AE-SH	Ash Shariqah	1225
3853	AE	AE-DU	Dubayy	1225
3854	AE	AE-RK	Ra's al Khaymah	1225
7519	VN	VN-33	Dac Lac	1233
3855	AE	AE-UQ	Umm al Qaywayn	1225
3856	AF	AF-BDS	Badakhshan	1001
3857	AF	AF-BDG	Badghis	1001
3858	AF	AF-BGL	Baghlan	1001
3859	AF	AF-BAL	Balkh	1001
3860	AF	AF-BAM	Bamian	1001
3861	AF	AF-FRA	Farah	1001
3862	AF	AF-FYB	Faryab	1001
3863	AF	AF-GHA	Ghazni	1001
3864	AF	AF-GHO	Ghowr	1001
3865	AF	AF-HEL	Helmand	1001
3866	AF	AF-HER	Herat	1001
3867	AF	AF-JOW	Jowzjan	1001
3868	AF	AF-KAB	Kabul	1001
3869	AF	AF-KAN	Kandahar	1001
3870	AF	AF-KAP	Kapisa	1001
3871	AF	AF-KHO	Khowst	1001
3872	AF	AF-KNR	Konar	1001
3873	AF	AF-KDZ	Kondoz	1001
3874	AF	AF-LAG	Laghman	1001
3875	AF	AF-LOW	Lowgar	1001
3876	AF	AF-NAN	Nangrahar	1001
3877	AF	AF-NIM	Nimruz	1001
3878	AF	AF-NUR	Nurestan	1001
3879	AF	AF-ORU	Oruzgan	1001
3880	AF	AF-PIA	Paktia	1001
3881	AF	AF-PKA	Paktika	1001
3882	AF	AF-PAR	Parwan	1001
3883	AF	AF-SAM	Samangan	1001
3884	AF	AF-SAR	Sar-e Pol	1001
3885	AF	AF-TAK	Takhar	1001
3886	AF	AF-WAR	Wardak	1001
3887	AF	AF-ZAB	Zabol	1001
3888	AL	AL-BR	Berat	1002
3889	AL	AL-BU	Bulqizë	1002
3890	AL	AL-DL	Delvinë	1002
3891	AL	AL-DV	Devoll	1002
3892	AL	AL-DI	Dibër	1002
3893	AL	AL-DR	Durrsës	1002
3894	AL	AL-EL	Elbasan	1002
3895	AL	AL-FR	Fier	1002
3896	AL	AL-GR	Gramsh	1002
3897	AL	AL-GJ	Gjirokastër	1002
3898	AL	AL-HA	Has	1002
3899	AL	AL-KA	Kavajë	1002
3900	AL	AL-ER	Kolonjë	1002
3901	AL	AL-KO	Korcë	1002
3902	AL	AL-KR	Krujë	1002
3903	AL	AL-KC	Kuçovë	1002
3904	AL	AL-KU	Kukës	1002
3905	AL	AL-KB	Kurbin	1002
3906	AL	AL-LE	Lezhë	1002
3907	AL	AL-LB	Librazhd	1002
3908	AL	AL-LU	Lushnjë	1002
3909	AL	AL-MM	Malësi e Madhe	1002
3910	AL	AL-MK	Mallakastër	1002
3911	AL	AL-MT	Mat	1002
3912	AL	AL-MR	Mirditë	1002
3913	AL	AL-PQ	Peqin	1002
3914	AL	AL-PR	Përmet	1002
3915	AL	AL-PG	Pogradec	1002
3916	AL	AL-PU	Pukë	1002
3917	AL	AL-SR	Sarandë	1002
3918	AL	AL-SK	Skrapar	1002
3919	AL	AL-SH	Shkodër	1002
3920	AL	AL-TE	Tepelenë	1002
3921	AL	AL-TR	Tiranë	1002
3922	AL	AL-TP	Tropojë	1002
3923	AL	AL-VL	Vlorë	1002
3924	AM	AM-ER	Erevan	1011
3925	AM	AM-AG	Aragacotn	1011
3926	AM	AM-AR	Ararat	1011
3927	AM	AM-AV	Armavir	1011
3928	AM	AM-GR	Gegarkunik'	1011
3929	AM	AM-KT	Kotayk'	1011
3930	AM	AM-LO	Lory	1011
3931	AM	AM-SH	Sirak	1011
3932	AM	AM-SU	Syunik'	1011
3933	AM	AM-TV	Tavus	1011
3934	AM	AM-VD	Vayoc Jor	1011
3935	AO	AO-BGO	Bengo	1006
3936	AO	AO-BGU	Benguela	1006
3937	AO	AO-BIE	Bie	1006
3938	AO	AO-CAB	Cabinda	1006
3939	AO	AO-CCU	Cuando-Cubango	1006
3940	AO	AO-CNO	Cuanza Norte	1006
3941	AO	AO-CUS	Cuanza Sul	1006
3942	AO	AO-CNN	Cunene	1006
3943	AO	AO-HUA	Huambo	1006
3944	AO	AO-HUI	Huila	1006
3945	AO	AO-LUA	Luanda	1006
3946	AO	AO-LNO	Lunda Norte	1006
3947	AO	AO-LSU	Lunda Sul	1006
3948	AO	AO-MAL	Malange	1006
3949	AO	AO-MOX	Moxico	1006
3950	AO	AO-NAM	Namibe	1006
3951	AO	AO-UIG	Uige	1006
3952	AO	AO-ZAI	Zaire	1006
3953	AR	AR-C	Capital federal	1010
3954	AR	AR-B	Buenos Aires	1010
3955	AR	AR-K	Catamarca	1010
3956	AR	AR-X	Cordoba	1010
3957	AR	AR-W	Corrientes	1010
3958	AR	AR-H	Chaco	1010
3959	AR	AR-U	Chubut	1010
3960	AR	AR-E	Entre Rios	1010
3961	AR	AR-P	Formosa	1010
3962	AR	AR-Y	Jujuy	1010
3963	AR	AR-L	La Pampa	1010
3964	AR	AR-M	Mendoza	1010
3965	AR	AR-N	Misiones	1010
3966	AR	AR-Q	Neuquen	1010
3967	AR	AR-R	Rio Negro	1010
3968	AR	AR-A	Salta	1010
3969	AR	AR-J	San Juan	1010
3970	AR	AR-D	San Luis	1010
3971	AR	AR-Z	Santa Cruz	1010
3972	AR	AR-S	Santa Fe	1010
3973	AR	AR-G	Santiago del Estero	1010
3974	AR	AR-V	Tierra del Fuego	1010
3975	AR	AR-T	Tucuman	1010
3976	AT	AT-1	Burgenland	1014
3977	AT	AT-2	Kärnten	1014
3978	AT	AT-3	Niederosterreich	1014
3979	AT	AT-4	Oberosterreich	1014
3980	AT	AT-5	Salzburg	1014
3981	AT	AT-6	Steiermark	1014
3982	AT	AT-7	Tirol	1014
3983	AT	AT-8	Vorarlberg	1014
3984	AT	AT-9	Wien	1014
3985	AU	AU-AAT	Australian Antarctic Territory	1013
3986	AU	AU-ACT	Australian Capital Territory	1013
3987	AU	AU-NT	Northern Territory	1013
3988	AU	AU-NSW	New South Wales	1013
3989	AU	AU-QLD	Queensland	1013
3990	AU	AU-SA	South Australia	1013
3991	AU	AU-TAS	Tasmania	1013
3992	AU	AU-VIC	Victoria	1013
3993	AU	AU-WA	Western Australia	1013
3994	AZ	AZ-NX	Naxcivan	1015
3995	AZ	AZ-AB	Ali Bayramli	1015
3996	AZ	AZ-BA	Baki	1015
3997	AZ	AZ-GA	Ganca	1015
3998	AZ	AZ-LA	Lankaran	1015
3999	AZ	AZ-MI	Mingacevir	1015
4000	AZ	AZ-NA	Naftalan	1015
4001	AZ	AZ-SA	Saki	1015
4002	AZ	AZ-SM	Sumqayit	1015
4003	AZ	AZ-SS	Susa	1015
4004	AZ	AZ-XA	Xankandi	1015
4005	AZ	AZ-YE	Yevlax	1015
4006	AZ	AZ-ABS	Abseron	1015
4007	AZ	AZ-AGC	Agcabadi	1015
4008	AZ	AZ-AGM	Agdam	1015
4009	AZ	AZ-AGS	Agdas	1015
4010	AZ	AZ-AGA	Agstafa	1015
4011	AZ	AZ-AGU	Agsu	1015
4012	AZ	AZ-AST	Astara	1015
4013	AZ	AZ-BAB	Babak	1015
4014	AZ	AZ-BAL	Balakan	1015
4015	AZ	AZ-BAR	Barda	1015
4016	AZ	AZ-BEY	Beylagan	1015
4017	AZ	AZ-BIL	Bilasuvar	1015
4018	AZ	AZ-CAB	Cabrayll	1015
4019	AZ	AZ-CAL	Calilabad	1015
4020	AZ	AZ-CUL	Culfa	1015
4021	AZ	AZ-DAS	Daskasan	1015
4022	AZ	AZ-DAV	Davaci	1015
4023	AZ	AZ-FUZ	Fuzuli	1015
4024	AZ	AZ-GAD	Gadabay	1015
4025	AZ	AZ-GOR	Goranboy	1015
4026	AZ	AZ-GOY	Goycay	1015
4027	AZ	AZ-HAC	Haciqabul	1015
4028	AZ	AZ-IMI	Imisli	1015
4029	AZ	AZ-ISM	Ismayilli	1015
4030	AZ	AZ-KAL	Kalbacar	1015
4031	AZ	AZ-KUR	Kurdamir	1015
4032	AZ	AZ-LAC	Lacin	1015
4033	AZ	AZ-LER	Lerik	1015
4034	AZ	AZ-MAS	Masalli	1015
4035	AZ	AZ-NEF	Neftcala	1015
4036	AZ	AZ-OGU	Oguz	1015
4037	AZ	AZ-ORD	Ordubad	1015
4038	AZ	AZ-QAB	Qabala	1015
4039	AZ	AZ-QAX	Qax	1015
4040	AZ	AZ-QAZ	Qazax	1015
4041	AZ	AZ-QOB	Qobustan	1015
4042	AZ	AZ-QBA	Quba	1015
4043	AZ	AZ-QBI	Qubadli	1015
4044	AZ	AZ-QUS	Qusar	1015
4045	AZ	AZ-SAT	Saatli	1015
4046	AZ	AZ-SAB	Sabirabad	1015
4047	AZ	AZ-SAD	Sadarak	1015
4048	AZ	AZ-SAH	Sahbuz	1015
4049	AZ	AZ-SAL	Salyan	1015
4050	AZ	AZ-SMI	Samaxi	1015
4051	AZ	AZ-SKR	Samkir	1015
4052	AZ	AZ-SMX	Samux	1015
4053	AZ	AZ-SAR	Sarur	1015
4054	AZ	AZ-SIY	Siyazan	1015
4055	AZ	AZ-TAR	Tartar	1015
4056	AZ	AZ-TOV	Tovuz	1015
4057	AZ	AZ-UCA	Ucar	1015
4058	AZ	AZ-XAC	Xacmaz	1015
4059	AZ	AZ-XAN	Xanlar	1015
4060	AZ	AZ-XIZ	Xizi	1015
4061	AZ	AZ-XCI	Xocali	1015
4062	AZ	AZ-XVD	Xocavand	1015
4063	AZ	AZ-YAR	Yardimli	1015
4064	AZ	AZ-ZAN	Zangilan	1015
4065	AZ	AZ-ZAQ	Zaqatala	1015
4066	AZ	AZ-ZAR	Zardab	1015
4067	BA	BA-BIH	Federacija Bosna i Hercegovina	1026
4068	BA	BA-SRP	Republika Srpska	1026
4069	BD	BD-05	Bagerhat zila	1017
4070	BD	BD-01	Bandarban zila	1017
4071	BD	BD-02	Barguna zila	1017
4072	BD	BD-06	Barisal zila	1017
4073	BD	BD-07	Bhola zila	1017
4074	BD	BD-03	Bogra zila	1017
4075	BD	BD-04	Brahmanbaria zila	1017
4076	BD	BD-09	Chandpur zila	1017
4077	BD	BD-10	Chittagong zila	1017
4078	BD	BD-12	Chuadanga zila	1017
4079	BD	BD-08	Comilla zila	1017
4080	BD	BD-11	Cox's Bazar zila	1017
4081	BD	BD-13	Dhaka zila	1017
4082	BD	BD-14	Dinajpur zila	1017
4083	BD	BD-15	Faridpur zila	1017
4084	BD	BD-16	Feni zila	1017
4085	BD	BD-19	Gaibandha zila	1017
4086	BD	BD-18	Gazipur zila	1017
4087	BD	BD-17	Gopalganj zila	1017
4088	BD	BD-20	Habiganj zila	1017
4089	BD	BD-24	Jaipurhat zila	1017
4090	BD	BD-21	Jamalpur zila	1017
4091	BD	BD-22	Jessore zila	1017
4092	BD	BD-25	Jhalakati zila	1017
4093	BD	BD-23	Jhenaidah zila	1017
4094	BD	BD-29	Khagrachari zila	1017
4095	BD	BD-27	Khulna zila	1017
4096	BD	BD-26	Kishorganj zila	1017
4097	BD	BD-28	Kurigram zila	1017
4098	BD	BD-30	Kushtia zila	1017
4099	BD	BD-31	Lakshmipur zila	1017
4100	BD	BD-32	Lalmonirhat zila	1017
4101	BD	BD-36	Madaripur zila	1017
4102	BD	BD-37	Magura zila	1017
4103	BD	BD-33	Manikganj zila	1017
4104	BD	BD-39	Meherpur zila	1017
4105	BD	BD-38	Moulvibazar zila	1017
4106	BD	BD-35	Munshiganj zila	1017
4107	BD	BD-34	Mymensingh zila	1017
4108	BD	BD-48	Naogaon zila	1017
4109	BD	BD-43	Narail zila	1017
4110	BD	BD-40	Narayanganj zila	1017
4111	BD	BD-42	Narsingdi zila	1017
4112	BD	BD-44	Natore zila	1017
4113	BD	BD-45	Nawabganj zila	1017
4114	BD	BD-41	Netrakona zila	1017
4115	BD	BD-46	Nilphamari zila	1017
4116	BD	BD-47	Noakhali zila	1017
4117	BD	BD-49	Pabna zila	1017
4118	BD	BD-52	Panchagarh zila	1017
4119	BD	BD-51	Patuakhali zila	1017
4120	BD	BD-50	Pirojpur zila	1017
4121	BD	BD-53	Rajbari zila	1017
4122	BD	BD-54	Rajshahi zila	1017
4123	BD	BD-56	Rangamati zila	1017
4124	BD	BD-55	Rangpur zila	1017
4125	BD	BD-58	Satkhira zila	1017
4126	BD	BD-62	Shariatpur zila	1017
4127	BD	BD-57	Sherpur zila	1017
4128	BD	BD-59	Sirajganj zila	1017
4129	BD	BD-61	Sunamganj zila	1017
4130	BD	BD-60	Sylhet zila	1017
4131	BD	BD-63	Tangail zila	1017
4132	BD	BD-64	Thakurgaon zila	1017
4133	BE	BE-VAN	Antwerpen	1020
4134	BE	BE-WBR	Brabant Wallon	1020
4135	BE	BE-WHT	Hainaut	1020
4136	BE	BE-WLG	Liege	1020
4137	BE	BE-VLI	Limburg	1020
4138	BE	BE-WLX	Luxembourg	1020
4139	BE	BE-WNA	Namur	1020
4140	BE	BE-VOV	Oost-Vlaanderen	1020
4141	BE	BE-VBR	Vlaams-Brabant	1020
4142	BE	BE-VWV	West-Vlaanderen	1020
4143	BF	BF-BAL	Bale	1034
4144	BF	BF-BAM	Bam	1034
4145	BF	BF-BAN	Banwa	1034
4146	BF	BF-BAZ	Bazega	1034
4147	BF	BF-BGR	Bougouriba	1034
4148	BF	BF-BLG	Boulgou	1034
4149	BF	BF-BLK	Boulkiemde	1034
4150	BF	BF-COM	Comoe	1034
4151	BF	BF-GAN	Ganzourgou	1034
4152	BF	BF-GNA	Gnagna	1034
4153	BF	BF-GOU	Gourma	1034
4154	BF	BF-HOU	Houet	1034
4155	BF	BF-IOB	Ioba	1034
4156	BF	BF-KAD	Kadiogo	1034
4157	BF	BF-KEN	Kenedougou	1034
4158	BF	BF-KMD	Komondjari	1034
4159	BF	BF-KMP	Kompienga	1034
4160	BF	BF-KOS	Kossi	1034
4161	BF	BF-KOP	Koulpulogo	1034
4162	BF	BF-KOT	Kouritenga	1034
4163	BF	BF-KOW	Kourweogo	1034
4164	BF	BF-LER	Leraba	1034
4165	BF	BF-LOR	Loroum	1034
4166	BF	BF-MOU	Mouhoun	1034
4167	BF	BF-NAO	Nahouri	1034
4168	BF	BF-NAM	Namentenga	1034
4169	BF	BF-NAY	Nayala	1034
4170	BF	BF-NOU	Noumbiel	1034
4171	BF	BF-OUB	Oubritenga	1034
4172	BF	BF-OUD	Oudalan	1034
4173	BF	BF-PAS	Passore	1034
4174	BF	BF-PON	Poni	1034
4175	BF	BF-SNG	Sanguie	1034
4176	BF	BF-SMT	Sanmatenga	1034
4177	BF	BF-SEN	Seno	1034
4178	BF	BF-SIS	Siasili	1034
4179	BF	BF-SOM	Soum	1034
4180	BF	BF-SOR	Sourou	1034
4181	BF	BF-TAP	Tapoa	1034
4182	BF	BF-TUI	Tui	1034
4183	BF	BF-YAG	Yagha	1034
4184	BF	BF-YAT	Yatenga	1034
4185	BF	BF-ZIR	Ziro	1034
4186	BF	BF-ZON	Zondoma	1034
4187	BF	BF-ZOU	Zoundweogo	1034
4188	BG	BG-01	Blagoevgrad	1033
4189	BG	BG-02	Burgas	1033
4190	BG	BG-08	Dobric	1033
4191	BG	BG-07	Gabrovo	1033
4192	BG	BG-26	Haskovo	1033
4193	BG	BG-28	Jambol	1033
4194	BG	BG-09	Kardzali	1033
4195	BG	BG-10	Kjstendil	1033
4196	BG	BG-11	Lovec	1033
4197	BG	BG-12	Montana	1033
4198	BG	BG-13	Pazardzik	1033
4199	BG	BG-14	Pernik	1033
4200	BG	BG-15	Pleven	1033
4201	BG	BG-16	Plovdiv	1033
4202	BG	BG-17	Razgrad	1033
4203	BG	BG-18	Ruse	1033
4204	BG	BG-19	Silistra	1033
4205	BG	BG-20	Sliven	1033
4206	BG	BG-21	Smoljan	1033
4207	BG	BG-23	Sofija	1033
4208	BG	BG-24	Stara Zagora	1033
4209	BG	BG-27	Sumen	1033
4210	BG	BG-25	Targoviste	1033
4211	BG	BG-03	Varna	1033
4212	BG	BG-04	Veliko Tarnovo	1033
4213	BG	BG-05	Vidin	1033
4214	BG	BG-06	Vraca	1033
4215	BH	BH-01	Al Hadd	1016
4216	BH	BH-03	Al Manamah	1016
4217	BH	BH-10	Al Mintaqah al Gharbiyah	1016
4218	BH	BH-07	Al Mintagah al Wusta	1016
4219	BH	BH-05	Al Mintaqah ash Shamaliyah	1016
4220	BH	BH-02	Al Muharraq	1016
4221	BH	BH-09	Ar Rifa	1016
4222	BH	BH-04	Jidd Hafs	1016
4223	BH	BH-12	Madluat Jamad	1016
4224	BH	BH-08	Madluat Isa	1016
4225	BH	BH-11	Mintaqat Juzur tawar	1016
4226	BH	BH-06	Sitrah	1016
4227	BI	BI-BB	Bubanza	1036
4228	BI	BI-BJ	Bujumbura	1036
4229	BI	BI-BR	Bururi	1036
4230	BI	BI-CA	Cankuzo	1036
4231	BI	BI-CI	Cibitoke	1036
4232	BI	BI-GI	Gitega	1036
4233	BI	BI-KR	Karuzi	1036
4234	BI	BI-KY	Kayanza	1036
4235	BI	BI-MA	Makamba	1036
4236	BI	BI-MU	Muramvya	1036
4237	BI	BI-MW	Mwaro	1036
4238	BI	BI-NG	Ngozi	1036
4239	BI	BI-RT	Rutana	1036
4240	BI	BI-RY	Ruyigi	1036
4241	BJ	BJ-AL	Alibori	1022
4242	BJ	BJ-AK	Atakora	1022
4243	BJ	BJ-AQ	Atlantique	1022
4244	BJ	BJ-BO	Borgou	1022
4245	BJ	BJ-CO	Collines	1022
4246	BJ	BJ-DO	Donga	1022
4247	BJ	BJ-KO	Kouffo	1022
4248	BJ	BJ-LI	Littoral	1022
4249	BJ	BJ-MO	Mono	1022
4250	BJ	BJ-OU	Oueme	1022
4251	BJ	BJ-PL	Plateau	1022
4252	BJ	BJ-ZO	Zou	1022
4253	BN	BN-BE	Belait	1032
4254	BN	BN-BM	Brunei-Muara	1032
4255	BN	BN-TE	Temburong	1032
4256	BN	BN-TU	Tutong	1032
4257	BO	BO-C	Cochabamba	1025
4258	BO	BO-H	Chuquisaca	1025
4259	BO	BO-B	El Beni	1025
4260	BO	BO-L	La Paz	1025
4261	BO	BO-O	Oruro	1025
4262	BO	BO-N	Pando	1025
4263	BO	BO-P	Potosi	1025
4264	BO	BO-T	Tarija	1025
4265	BR	BR-AC	Acre	1029
4266	BR	BR-AL	Alagoas	1029
4267	BR	BR-AM	Amazonas	1029
4268	BR	BR-AP	Amapa	1029
4269	BR	BR-BA	Baia	1029
4270	BR	BR-CE	Ceara	1029
4271	BR	BR-DF	Distrito Federal	1029
4272	BR	BR-ES	Espirito Santo	1029
4273	BR	BR-FN	Fernando de Noronha	1029
4274	BR	BR-GO	Goias	1029
4275	BR	BR-MA	Maranhao	1029
4276	BR	BR-MG	Minas Gerais	1029
4277	BR	BR-MS	Mato Grosso do Sul	1029
4278	BR	BR-MT	Mato Grosso	1029
4279	BR	BR-PA	Para	1029
4280	BR	BR-PB	Paraiba	1029
4281	BR	BR-PE	Pernambuco	1029
4282	BR	BR-PI	Piaui	1029
4283	BR	BR-PR	Parana	1029
4284	BR	BR-RJ	Rio de Janeiro	1029
4285	BR	BR-RN	Rio Grande do Norte	1029
4286	BR	BR-RO	Rondonia	1029
4287	BR	BR-RR	Roraima	1029
4288	BR	BR-RS	Rio Grande do Sul	1029
4289	BR	BR-SC	Santa Catarina	1029
4290	BR	BR-SE	Sergipe	1029
4291	BR	BR-SP	Sao Paulo	1029
4292	BR	BR-TO	Tocatins	1029
4293	BS	BS-AC	Acklins and Crooked Islands	1212
4294	BS	BS-BI	Bimini	1212
4295	BS	BS-CI	Cat Island	1212
4296	BS	BS-EX	Exuma	1212
4297	BS	BS-FP	Freeport	1212
4298	BS	BS-FC	Fresh Creek	1212
4299	BS	BS-GH	Governor's Harbour	1212
4300	BS	BS-GT	Green Turtle Cay	1212
4301	BS	BS-HI	Harbour Island	1212
4302	BS	BS-HR	High Rock	1212
4303	BS	BS-IN	Inagua	1212
4304	BS	BS-KB	Kemps Bay	1212
4305	BS	BS-LI	Long Island	1212
4306	BS	BS-MH	Marsh Harbour	1212
4307	BS	BS-MG	Mayaguana	1212
4308	BS	BS-NP	New Providence	1212
4309	BS	BS-NB	Nicholls Town and Berry Islands	1212
4310	BS	BS-RI	Ragged Island	1212
4311	BS	BS-RS	Rock Sound	1212
4312	BS	BS-SP	Sandy Point	1212
4313	BS	BS-SR	San Salvador and Rum Cay	1212
4314	BT	BT-33	Bumthang	1024
4315	BT	BT-12	Chhukha	1024
4316	BT	BT-22	Dagana	1024
4317	BT	BT-GA	Gasa	1024
4318	BT	BT-13	Ha	1024
4319	BT	BT-44	Lhuentse	1024
4320	BT	BT-42	Monggar	1024
4321	BT	BT-11	Paro	1024
4322	BT	BT-43	Pemagatshel	1024
4323	BT	BT-23	Punakha	1024
4324	BT	BT-45	Samdrup Jongkha	1024
4325	BT	BT-14	Samtee	1024
4326	BT	BT-31	Sarpang	1024
4327	BT	BT-15	Thimphu	1024
4328	BT	BT-41	Trashigang	1024
4329	BT	BT-TY	Trashi Yangtse	1024
4330	BT	BT-32	Trongsa	1024
4331	BT	BT-21	Tsirang	1024
4332	BT	BT-24	Wangdue Phodrang	1024
4333	BT	BT-34	Zhemgang	1024
4334	BW	BW-CE	Central	1027
4335	BW	BW-GH	Ghanzi	1027
4336	BW	BW-KG	Kgalagadi	1027
4337	BW	BW-KL	Kgatleng	1027
4338	BW	BW-KW	Kweneng	1027
4339	BW	BW-NG	Ngamiland	1027
4340	BW	BW-NE	North-East	1027
4341	BW	BW-NW	North-West	1027
4342	BW	BW-SE	South-East	1027
4343	BW	BW-SO	Southern	1027
4344	BY	BY-BR	Brèsckaja voblasc'	1019
4345	BY	BY-HO	Homel'skaja voblasc'	1019
4346	BY	BY-HR	Hrodzenskaja voblasc'	1019
4347	BY	BY-MA	Mahilëuskaja voblasc'	1019
4348	BY	BY-MI	Minskaja voblasc'	1019
4349	BY	BY-VI	Vicebskaja voblasc'	1019
4350	BZ	BZ-BZ	Belize	1021
4351	BZ	BZ-CY	Cayo	1021
4352	BZ	BZ-CZL	Corozal	1021
4353	BZ	BZ-OW	Orange Walk	1021
4354	BZ	BZ-SC	Stann Creek	1021
4355	BZ	BZ-TOL	Toledo	1021
4356	CA	CA-AB	Alberta	1039
4357	CA	CA-BC	British Columbia	1039
4358	CA	CA-MB	Manitoba	1039
4359	CA	CA-NB	New Brunswick	1039
4360	CA	CA-NL	Newfoundland and Labrador	1039
4361	CA	CA-NS	Nova Scotia	1039
4362	CA	CA-ON	Ontario	1039
4363	CA	CA-PE	Prince Edward Island	1039
4364	CA	CA-QC	Quebec	1039
4365	CA	CA-SK	Saskatchewan	1039
4366	CA	CA-NT	Northwest Territories	1039
4367	CA	CA-NU	Nunavut	1039
4368	CA	CA-YT	Yukon Territory	1039
4369	CD	CD-KN	Kinshasa	1050
4370	CD	CD-BN	Bandundu	1050
4371	CD	CD-BC	Bas-Congo	1050
4372	CD	CD-EQ	Equateur	1050
4373	CD	CD-HC	Haut-Congo	1050
4374	CD	CD-KW	Kasai-Occidental	1050
4375	CD	CD-KE	Kasai-Oriental	1050
4376	CD	CD-KA	Katanga	1050
4377	CD	CD-MA	Maniema	1050
4378	CD	CD-NK	Nord-Kivu	1050
4379	CD	CD-OR	Orientale	1050
4380	CD	CD-SK	Sud-Kivu	1050
4381	CF	CF-BGF	Bangui	1042
4382	CF	CF-BB	Bamingui-Bangoran	1042
4383	CF	CF-BK	Basse-Kotto	1042
4384	CF	CF-HK	Haute-Kotto	1042
4385	CF	CF-HM	Haut-Mbomou	1042
4386	CF	CF-KG	Kemo	1042
4387	CF	CF-LB	Lobaye	1042
4388	CF	CF-HS	Mambere-Kadei	1042
4389	CF	CF-MB	Mbomou	1042
4390	CF	CF-KB	Nana-Grebizi	1042
4391	CF	CF-NM	Nana-Mambere	1042
4392	CF	CF-MP	Ombella-Mpoko	1042
4393	CF	CF-UK	Ouaka	1042
4394	CF	CF-AC	Ouham	1042
4395	CF	CF-OP	Ouham-Pende	1042
4396	CF	CF-SE	Sangha-Mbaere	1042
4397	CF	CF-VR	Vakaga	1042
4398	CG	CG-BZV	Brazzaville	1051
4399	CG	CG-11	Bouenza	1051
4400	CG	CG-8	Cuvette	1051
4401	CG	CG-15	Cuvette-Ouest	1051
4402	CG	CG-5	Kouilou	1051
4403	CG	CG-2	Lekoumou	1051
4404	CG	CG-7	Likouala	1051
4405	CG	CG-9	Niari	1051
4406	CG	CG-14	Plateaux	1051
4407	CG	CG-12	Pool	1051
4408	CG	CG-13	Sangha	1051
4409	CH	CH-AG	Aargau	1205
4410	CH	CH-AI	Appenzell Innerrhoden	1205
4411	CH	CH-AR	Appenzell Ausserrhoden	1205
4412	CH	CH-BE	Bern	1205
4413	CH	CH-BL	Basel-Landschaft	1205
4414	CH	CH-BS	Basel-Stadt	1205
4415	CH	CH-FR	Fribourg	1205
4416	CH	CH-GE	Geneva	1205
4417	CH	CH-GL	Glarus	1205
4418	CH	CH-GR	Graubunden	1205
4419	CH	CH-JU	Jura	1205
4420	CH	CH-LU	Luzern	1205
4421	CH	CH-NE	Neuchatel	1205
4422	CH	CH-NW	Nidwalden	1205
4423	CH	CH-OW	Obwalden	1205
4424	CH	CH-SG	Sankt Gallen	1205
4425	CH	CH-SH	Schaffhausen	1205
4426	CH	CH-SO	Solothurn	1205
4427	CH	CH-SZ	Schwyz	1205
4428	CH	CH-TG	Thurgau	1205
4429	CH	CH-TI	Ticino	1205
4430	CH	CH-UR	Uri	1205
4431	CH	CH-VD	Vaud	1205
4432	CH	CH-VS	Valais	1205
4433	CH	CH-ZG	Zug	1205
4434	CH	CH-ZH	Zurich	1205
4435	CI	CI-06	18 Montagnes	1054
4436	CI	CI-16	Agnebi	1054
4437	CI	CI-09	Bas-Sassandra	1054
4438	CI	CI-10	Denguele	1054
4439	CI	CI-02	Haut-Sassandra	1054
4440	CI	CI-07	Lacs	1054
4441	CI	CI-01	Lagunes	1054
4442	CI	CI-12	Marahoue	1054
4443	CI	CI-05	Moyen-Comoe	1054
4444	CI	CI-11	Nzi-Comoe	1054
4445	CI	CI-03	Savanes	1054
4446	CI	CI-15	Sud-Bandama	1054
4447	CI	CI-13	Sud-Comoe	1054
4448	CI	CI-04	Vallee du Bandama	1054
4449	CI	CI-14	Worodouqou	1054
4450	CI	CI-08	Zanzan	1054
4451	CL	CL-AI	Aisen del General Carlos Ibanez del Campo	1044
4452	CL	CL-AN	Antofagasta	1044
4453	CL	CL-AR	Araucania	1044
4454	CL	CL-AT	Atacama	1044
4455	CL	CL-BI	Bio-Bio	1044
4456	CL	CL-CO	Coquimbo	1044
4457	CL	CL-LI	Libertador General Bernardo O'Higgins	1044
4458	CL	CL-LL	Los Lagos	1044
4459	CL	CL-MA	Magallanes	1044
4460	CL	CL-ML	Maule	1044
4461	CL	CL-RM	Region Metropolitana de Santiago	1044
4462	CL	CL-TA	Tarapaca	1044
4463	CL	CL-VS	Valparaiso	1044
4464	CM	CM-AD	Adamaoua	1038
4465	CM	CM-CE	Centre	1038
4466	CM	CM-ES	East	1038
4467	CM	CM-EN	Far North	1038
4468	CM	CM-NO	North	1038
4469	CM	CM-SW	South	1038
4470	CM	CM-SW	South-West	1038
4471	CM	CM-OU	West	1038
4472	CN	CN-11	Beijing	1045
4473	CN	CN-50	Chongqing	1045
4474	CN	CN-31	Shanghai	1045
4475	CN	CN-12	Tianjin	1045
4476	CN	CN-34	Anhui	1045
4477	CN	CN-35	Fujian	1045
4478	CN	CN-62	Gansu	1045
4479	CN	CN-44	Guangdong	1045
4480	CN	CN-52	Gulzhou	1045
4481	CN	CN-46	Hainan	1045
4482	CN	CN-13	Hebei	1045
4483	CN	CN-23	Heilongjiang	1045
4484	CN	CN-41	Henan	1045
4485	CN	CN-42	Hubei	1045
4486	CN	CN-43	Hunan	1045
4487	CN	CN-32	Jiangsu	1045
4488	CN	CN-36	Jiangxi	1045
4489	CN	CN-22	Jilin	1045
4490	CN	CN-21	Liaoning	1045
4491	CN	CN-63	Qinghai	1045
4492	CN	CN-61	Shaanxi	1045
4493	CN	CN-37	Shandong	1045
4494	CN	CN-14	Shanxi	1045
4495	CN	CN-51	Sichuan	1045
4496	CN	CN-71	Taiwan	1045
4497	CN	CN-53	Yunnan	1045
4498	CN	CN-33	Zhejiang	1045
4499	CN	CN-45	Guangxi	1045
4500	CN	CN-15	Neia Mongol (mn)	1045
4501	CN	CN-65	Xinjiang	1045
4502	CN	CN-54	Xizang	1045
4503	CN	CN-91	Hong Kong	1045
4504	CN	CN-92	Macau	1045
4505	CO	CO-DC	Distrito Capital de Bogotá	1048
4506	CO	CO-AMA	Amazonea	1048
4507	CO	CO-ANT	Antioquia	1048
4508	CO	CO-ARA	Arauca	1048
4509	CO	CO-ATL	Atlántico	1048
4510	CO	CO-BOL	Bolívar	1048
4511	CO	CO-BOY	Boyacá	1048
4512	CO	CO-CAL	Caldea	1048
4513	CO	CO-CAQ	Caquetá	1048
4514	CO	CO-CAS	Casanare	1048
4515	CO	CO-CAU	Cauca	1048
4516	CO	CO-CES	Cesar	1048
4517	CO	CO-COR	Córdoba	1048
4518	CO	CO-CUN	Cundinamarca	1048
4519	CO	CO-CHO	Chocó	1048
4520	CO	CO-GUA	Guainía	1048
4521	CO	CO-GUV	Guaviare	1048
4522	CO	CO-LAG	La Guajira	1048
4523	CO	CO-MAG	Magdalena	1048
4524	CO	CO-MET	Meta	1048
4525	CO	CO-NAR	Nariño	1048
4526	CO	CO-NSA	Norte de Santander	1048
4527	CO	CO-PUT	Putumayo	1048
4528	CO	CO-QUI	Quindio	1048
4529	CO	CO-RIS	Risaralda	1048
4530	CO	CO-SAP	San Andrés, Providencia y Santa Catalina	1048
4531	CO	CO-SAN	Santander	1048
4532	CO	CO-SUC	Sucre	1048
4533	CO	CO-TOL	Tolima	1048
4534	CO	CO-VAC	Valle del Cauca	1048
4535	CO	CO-VAU	Vaupés	1048
4536	CO	CO-VID	Vichada	1048
4537	CR	CR-A	Alajuela	1053
4538	CR	CR-C	Cartago	1053
4539	CR	CR-G	Guanacaste	1053
4540	CR	CR-H	Heredia	1053
4541	CR	CR-L	Limon	1053
4542	CR	CR-P	Puntarenas	1053
4543	CR	CR-SJ	San Jose	1053
4544	CU	CU-09	Camagey	1056
4545	CU	CU-08	Ciego de `vila	1056
4546	CU	CU-06	Cienfuegos	1056
4547	CU	CU-03	Ciudad de La Habana	1056
4548	CU	CU-12	Granma	1056
4549	CU	CU-14	Guantanamo	1056
4550	CU	CU-11	Holquin	1056
4551	CU	CU-02	La Habana	1056
4552	CU	CU-10	Las Tunas	1056
4553	CU	CU-04	Matanzas	1056
4554	CU	CU-01	Pinar del Rio	1056
4555	CU	CU-07	Sancti Spiritus	1056
4556	CU	CU-13	Santiago de Cuba	1056
4557	CU	CU-05	Villa Clara	1056
4558	CU	CU-99	Isla de la Juventud	1056
4559	CU	CU-PR	Pinar del Roo	1056
4560	CU	CU-CA	Ciego de Avila	1056
4561	CU	CU-CG	Camagoey	1056
4562	CU	CU-HO	Holgun	1056
4563	CU	CU-SS	Sancti Spritus	1056
4564	CU	CU-IJ	Municipio Especial Isla de la Juventud	1056
4565	CV	CV-BV	Boa Vista	1040
4566	CV	CV-BR	Brava	1040
4567	CV	CV-CS	Calheta de Sao Miguel	1040
4568	CV	CV-FO	Fogo	1040
4569	CV	CV-MA	Maio	1040
4570	CV	CV-MO	Mosteiros	1040
4571	CV	CV-PA	Paul	1040
4572	CV	CV-PN	Porto Novo	1040
4573	CV	CV-PR	Praia	1040
4574	CV	CV-RG	Ribeira Grande	1040
4575	CV	CV-SL	Sal	1040
4576	CV	CV-SD	Sao Domingos	1040
4577	CV	CV-SF	Sao Filipe	1040
4578	CV	CV-SN	Sao Nicolau	1040
4579	CV	CV-SV	Sao Vicente	1040
4580	CV	CV-TA	Tarrafal	1040
4581	CY	CY-04	Ammochostos Magusa	1057
4582	CY	CY-06	Keryneia	1057
4583	CY	CY-03	Larnaka	1057
4584	CY	CY-01	Lefkosia	1057
4585	CY	CY-02	Lemesos	1057
4586	CY	CY-05	Pafos	1057
4587	CZ	CZ-JC	Jihočeský kraj	1058
4588	CZ	CZ-JM	Jihomoravský kraj	1058
4589	CZ	CZ-KA	Karlovarský kraj	1058
4590	CZ	CZ-KR	Královéhradecký kraj	1058
4591	CZ	CZ-LI	Liberecký kraj	1058
4592	CZ	CZ-MO	Moravskoslezský kraj	1058
4593	CZ	CZ-OL	Olomoucký kraj	1058
4594	CZ	CZ-PA	Pardubický kraj	1058
4595	CZ	CZ-PL	Plzeňský kraj	1058
4596	CZ	CZ-PR	Praha, hlavní město	1058
4597	CZ	CZ-ST	Středočeský kraj	1058
4598	CZ	CZ-US	Ústecký kraj	1058
4599	CZ	CZ-VY	Vysočina	1058
4600	CZ	CZ-ZL	Zlínský kraj	1058
4601	DE	DE-BW	Baden-Wuerttemberg	1082
4602	DE	DE-BY	Bayern	1082
4603	DE	DE-HB	Bremen	1082
4604	DE	DE-HH	Hamburg	1082
4605	DE	DE-HE	Hessen	1082
4606	DE	DE-NI	Niedersachsen	1082
4607	DE	DE-NW	Nordrhein-Westfalen	1082
4608	DE	DE-RP	Rheinland-Pfalz	1082
4609	DE	DE-SL	Saarland	1082
4610	DE	DE-SH	Schleswig-Holstein	1082
4611	DE	DE-BR	Berlin	1082
4612	DE	DE-BB	Brandenburg	1082
4613	DE	DE-MV	Mecklenburg-Vorpommern	1082
4614	DE	DE-SN	Sachsen	1082
4615	DE	DE-ST	Sachsen-Anhalt	1082
4616	DE	DE-TH	Thueringen	1082
4617	DJ	DJ-AS	Ali Sabiah	1060
4618	DJ	DJ-DI	Dikhil	1060
4619	DJ	DJ-DJ	Djibouti	1060
4620	DJ	DJ-OB	Obock	1060
4621	DJ	DJ-TA	Tadjoura	1060
4622	DK	DK-147	Frederikaberg municipality	1059
4623	DK	DK-101	Copenhagen municipality	1059
4624	DK	DK-015	Copenhagen	1059
4625	DK	DK-020	Frederiksborg	1059
4626	DK	DK-025	Roskilde	1059
4627	DK	DK-030	Western Zealand	1059
4628	DK	DK-035	Storstrøm	1059
4629	DK	DK-040	Bornholm	1059
4630	DK	DK-042	Funen	1059
4631	DK	DK-050	Southern Jutland	1059
4632	DK	DK-055	Ribe	1059
4633	DK	DK-060	Vejle	1059
4634	DK	DK-065	Ringkøbing	1059
4635	DK	DK-070	Aarhus	1059
4636	DK	DK-076	Viborg	1059
4637	DK	DK-080	Northern Jutland	1059
4638	DO	DO-01	Distrito Nacional (Santo Domingo)	1062
4639	DO	DO-02	Azua	1062
4640	DO	DO-03	Bahoruco	1062
4641	DO	DO-04	Barahona	1062
4642	DO	DO-05	Dajabón	1062
4643	DO	DO-06	Duarte	1062
4644	DO	DO-08	El Seybo [El Seibo]	1062
4645	DO	DO-09	Espaillat	1062
4646	DO	DO-30	Hato Mayor	1062
4647	DO	DO-10	Independencia	1062
4648	DO	DO-11	La Altagracia	1062
4649	DO	DO-07	La Estrelleta [Elias Pina]	1062
4650	DO	DO-12	La Romana	1062
4651	DO	DO-13	La Vega	1062
4652	DO	DO-14	Maroia Trinidad Sánchez	1062
4653	DO	DO-28	Monseñor Nouel	1062
4654	DO	DO-15	Monte Cristi	1062
4655	DO	DO-29	Monte Plata	1062
4656	DO	DO-16	Pedernales	1062
4657	DO	DO-17	Peravia	1062
4658	DO	DO-18	Puerto Plata	1062
4659	DO	DO-19	Salcedo	1062
4660	DO	DO-20	Samaná	1062
4661	DO	DO-21	San Cristóbal	1062
4662	DO	DO-23	San Pedro de Macorís	1062
4663	DO	DO-24	Sánchez Ramírez	1062
4664	DO	DO-25	Santiago	1062
4665	DO	DO-26	Santiago Rodríguez	1062
4666	DO	DO-27	Valverde	1062
4667	DZ	DZ-01	Adrar	1003
4668	DZ	DZ-44	Ain Defla	1003
4669	DZ	DZ-46	Ain Tmouchent	1003
4670	DZ	DZ-16	Alger	1003
4671	DZ	DZ-23	Annaba	1003
4672	DZ	DZ-05	Batna	1003
4673	DZ	DZ-08	Bechar	1003
4674	DZ	DZ-06	Bejaia	1003
4675	DZ	DZ-07	Biskra	1003
4676	DZ	DZ-09	Blida	1003
4677	DZ	DZ-34	Bordj Bou Arreridj	1003
4678	DZ	DZ-10	Bouira	1003
4679	DZ	DZ-35	Boumerdes	1003
4680	DZ	DZ-02	Chlef	1003
4681	DZ	DZ-25	Constantine	1003
4682	DZ	DZ-17	Djelfa	1003
4683	DZ	DZ-32	El Bayadh	1003
4684	DZ	DZ-39	El Oued	1003
4685	DZ	DZ-36	El Tarf	1003
4686	DZ	DZ-47	Ghardaia	1003
4687	DZ	DZ-24	Guelma	1003
4688	DZ	DZ-33	Illizi	1003
4689	DZ	DZ-18	Jijel	1003
4690	DZ	DZ-40	Khenchela	1003
4691	DZ	DZ-03	Laghouat	1003
4692	DZ	DZ-29	Mascara	1003
4693	DZ	DZ-26	Medea	1003
4694	DZ	DZ-43	Mila	1003
4695	DZ	DZ-27	Mostaganem	1003
4696	DZ	DZ-28	Msila	1003
4697	DZ	DZ-45	Naama	1003
4698	DZ	DZ-31	Oran	1003
4699	DZ	DZ-30	Ouargla	1003
4700	DZ	DZ-04	Oum el Bouaghi	1003
4701	DZ	DZ-48	Relizane	1003
4702	DZ	DZ-20	Saida	1003
4703	DZ	DZ-19	Setif	1003
4704	DZ	DZ-22	Sidi Bel Abbes	1003
4705	DZ	DZ-21	Skikda	1003
4706	DZ	DZ-41	Souk Ahras	1003
4707	DZ	DZ-11	Tamanghasset	1003
4708	DZ	DZ-12	Tebessa	1003
4709	DZ	DZ-14	Tiaret	1003
4710	DZ	DZ-37	Tindouf	1003
4711	DZ	DZ-42	Tipaza	1003
4712	DZ	DZ-38	Tissemsilt	1003
4713	DZ	DZ-15	Tizi Ouzou	1003
4714	DZ	DZ-13	Tlemcen	1003
4715	EC	EC-A	Azuay	1064
4716	EC	EC-B	Bolivar	1064
4717	EC	EC-F	Canar	1064
4718	EC	EC-C	Carchi	1064
4719	EC	EC-X	Cotopaxi	1064
4720	EC	EC-H	Chimborazo	1064
4721	EC	EC-O	El Oro	1064
4722	EC	EC-E	Esmeraldas	1064
4723	EC	EC-W	Galapagos	1064
4724	EC	EC-G	Guayas	1064
4725	EC	EC-I	Imbabura	1064
4726	EC	EC-L	Loja	1064
4727	EC	EC-R	Los Rios	1064
4728	EC	EC-M	Manabi	1064
4729	EC	EC-S	Morona-Santiago	1064
4730	EC	EC-N	Napo	1064
4731	EC	EC-D	Orellana	1064
4732	EC	EC-Y	Pastaza	1064
4733	EC	EC-P	Pichincha	1064
4734	EC	EC-U	Sucumbios	1064
4735	EC	EC-T	Tungurahua	1064
4736	EC	EC-Z	Zamora-Chinchipe	1064
4737	EE	EE-37	Harjumsa	1069
4738	EE	EE-39	Hitumea	1069
4739	EE	EE-44	Ida-Virumsa	1069
4740	EE	EE-49	Jogevamsa	1069
4741	EE	EE-51	Jarvamsa	1069
4742	EE	EE-57	Lasnemsa	1069
4743	EE	EE-59	Laane-Virumaa	1069
4744	EE	EE-65	Polvamea	1069
4745	EE	EE-67	Parnumsa	1069
4746	EE	EE-70	Raplamsa	1069
4747	EE	EE-74	Saaremsa	1069
4748	EE	EE-7B	Tartumsa	1069
4749	EE	EE-82	Valgamaa	1069
4750	EE	EE-84	Viljandimsa	1069
4751	EE	EE-86	Vorumaa	1069
4752	EG	EG-DK	Ad Daqahllyah	1065
4753	EG	EG-BA	Al Bahr al Ahmar	1065
4754	EG	EG-BH	Al Buhayrah	1065
4755	EG	EG-FYM	Al Fayym	1065
4756	EG	EG-GH	Al Gharbiyah	1065
4757	EG	EG-ALX	Al Iskandarlyah	1065
4758	EG	EG-IS	Al Isma illyah	1065
4759	EG	EG-GZ	Al Jizah	1065
4760	EG	EG-MNF	Al Minuflyah	1065
4761	EG	EG-MN	Al Minya	1065
4762	EG	EG-C	Al Qahirah	1065
4763	EG	EG-KB	Al Qalyublyah	1065
4764	EG	EG-WAD	Al Wadi al Jadid	1065
4765	EG	EG-SHR	Ash Sharqiyah	1065
4766	EG	EG-SUZ	As Suways	1065
4767	EG	EG-ASN	Aswan	1065
4768	EG	EG-AST	Asyut	1065
4769	EG	EG-BNS	Bani Suwayf	1065
4770	EG	EG-PTS	Bur Sa'id	1065
4771	EG	EG-DT	Dumyat	1065
4772	EG	EG-JS	Janub Sina'	1065
4773	EG	EG-KFS	Kafr ash Shaykh	1065
4774	EG	EG-MT	Matruh	1065
4775	EG	EG-KN	Qina	1065
4776	EG	EG-SIN	Shamal Sina'	1065
4777	EG	EG-SHG	Suhaj	1065
4778	ER	ER-AN	Anseba	1068
4779	ER	ER-DU	Debub	1068
4780	ER	ER-DK	Debubawi Keyih Bahri [Debub-Keih-Bahri]	1068
4781	ER	ER-GB	Gash-Barka	1068
4782	ER	ER-MA	Maakel [Maekel]	1068
4783	ER	ER-SK	Semenawi Keyih Bahri [Semien-Keih-Bahri]	1068
4784	ES	ES-VI	Álava	1198
4785	ES	ES-AB	Albacete	1198
4786	ES	ES-A	Alicante	1198
4787	ES	ES-AL	Almería	1198
4788	ES	ES-O	Asturias	1198
4789	ES	ES-AV	Ávila	1198
4790	ES	ES-BA	Badajoz	1198
4791	ES	ES-PM	Baleares	1198
4792	ES	ES-B	Barcelona	1198
4793	ES	ES-BU	Burgos	1198
4794	ES	ES-CC	Cáceres	1198
4795	ES	ES-CA	Cádiz	1198
4796	ES	ES-S	Cantabria	1198
4797	ES	ES-CS	Castellón	1198
4798	ES	ES-CR	Ciudad Real	1198
4799	ES	ES-CU	Cuenca	1198
4800	ES	ES-GE	Girona [Gerona]	1198
4801	ES	ES-GR	Granada	1198
4802	ES	ES-GU	Guadalajara	1198
4803	ES	ES-SS	Guipúzcoa	1198
4804	ES	ES-H	Huelva	1198
4805	ES	ES-HU	Huesca	1198
4806	ES	ES-J	Jaén	1198
4807	ES	ES-C	La Coruña	1198
4808	ES	ES-LO	La Rioja	1198
4809	ES	ES-GC	Las Palmas	1198
4810	ES	ES-LE	León	1198
4811	ES	ES-L	Lleida [Lérida]	1198
4812	ES	ES-LU	Lugo	1198
4813	ES	ES-M	Madrid	1198
4814	ES	ES-MA	Málaga	1198
4815	ES	ES-MU	Murcia	1198
4816	ES	ES-NA	Navarra	1198
4817	ES	ES-OR	Ourense	1198
4818	ES	ES-P	Palencia	1198
4819	ES	ES-PO	Pontevedra	1198
4820	ES	ES-SA	Salamanca	1198
4821	ES	ES-TF	Santa Cruz de Tenerife	1198
4822	ES	ES-SG	Segovia	1198
4823	ES	ES-SE	Sevilla	1198
4824	ES	ES-SO	Soria	1198
4825	ES	ES-T	Tarragona	1198
4826	ES	ES-TE	Teruel	1198
4827	ES	ES-V	Valencia	1198
4828	ES	ES-VA	Valladolid	1198
4829	ES	ES-BI	Vizcaya	1198
4830	ES	ES-ZA	Zamora	1198
4831	ES	ES-Z	Zaragoza	1198
4832	ES	ES-CE	Ceuta	1198
4833	ES	ES-ML	Melilla	1198
4834	ET	ET-AA	Addis Ababa	1070
4835	ET	ET-DD	Dire Dawa	1070
4836	ET	ET-AF	Afar	1070
4837	ET	ET-AM	Amara	1070
4838	ET	ET-BE	Benshangul-Gumaz	1070
4839	ET	ET-GA	Gambela Peoples	1070
4840	ET	ET-HA	Harari People	1070
4841	ET	ET-OR	Oromia	1070
4842	ET	ET-SO	Somali	1070
4843	ET	ET-SN	Southern Nations, Nationalities and Peoples	1070
4844	ET	ET-TI	Tigrai	1070
4845	FI	FI-AL	Ahvenanmasn laani	1075
4846	FI	FI-ES	Etela-Suomen laani	1075
4847	FI	FI-IS	Ita-Suomen lasni	1075
4848	FI	FI-LL	Lapin Laani	1075
4849	FI	FI-LS	Lansi-Suomen Laani	1075
4850	FI	FI-OL	Oulun Lasni	1075
4851	FJ	FJ-E	Eastern	1074
4852	FJ	FJ-N	Northern	1074
4853	FJ	FJ-W	Western	1074
4854	FJ	FJ-R	Rotuma	1074
4855	FM	FM-TRK	Chuuk	1141
4856	FM	FM-KSA	Kosrae	1141
4857	FM	FM-PNI	Pohnpei	1141
4858	FM	FM-YAP	Yap	1141
4859	FR	FR-01	Ain	1076
4860	FR	FR-02	Aisne	1076
4861	FR	FR-03	Allier	1076
4862	FR	FR-04	Alpes-de-Haute-Provence	1076
4863	FR	FR-06	Alpes-Maritimes	1076
4864	FR	FR-07	Ardèche	1076
4865	FR	FR-08	Ardennes	1076
4866	FR	FR-09	Ariège	1076
4867	FR	FR-10	Aube	1076
4868	FR	FR-11	Aude	1076
4869	FR	FR-12	Aveyron	1076
4870	FR	FR-67	Bas-Rhin	1076
4871	FR	FR-13	Bouches-du-Rhône	1076
4872	FR	FR-14	Calvados	1076
4873	FR	FR-15	Cantal	1076
4874	FR	FR-16	Charente	1076
4875	FR	FR-17	Charente-Maritime	1076
4876	FR	FR-18	Cher	1076
4877	FR	FR-19	Corrèze	1076
4878	FR	FR-20A	Corse-du-Sud	1076
4879	FR	FR-21	Côte-d'Or	1076
4880	FR	FR-22	Côtes-d'Armor	1076
4881	FR	FR-23	Creuse	1076
4882	FR	FR-79	Deux-Sèvres	1076
4883	FR	FR-24	Dordogne	1076
4884	FR	FR-25	Doubs	1076
4885	FR	FR-26	Drôme	1076
4886	FR	FR-91	Essonne	1076
4887	FR	FR-27	Eure	1076
4888	FR	FR-28	Eure-et-Loir	1076
4889	FR	FR-29	Finistère	1076
4890	FR	FR-30	Gard	1076
4891	FR	FR-32	Gers	1076
4892	FR	FR-33	Gironde	1076
4893	FR	FR-68	Haut-Rhin	1076
4894	FR	FR-20B	Haute-Corse	1076
4895	FR	FR-31	Haute-Garonne	1076
4896	FR	FR-43	Haute-Loire	1076
4897	FR	FR-70	Haute-Saône	1076
4898	FR	FR-74	Haute-Savoie	1076
4899	FR	FR-87	Haute-Vienne	1076
4900	FR	FR-05	Hautes-Alpes	1076
4901	FR	FR-65	Hautes-Pyrénées	1076
4902	FR	FR-92	Hauts-de-Seine	1076
4903	FR	FR-34	Hérault	1076
4904	FR	FR-35	Indre	1076
4905	FR	FR-36	Ille-et-Vilaine	1076
4906	FR	FR-37	Indre-et-Loire	1076
4907	FR	FR-38	Isère	1076
4908	FR	FR-40	Landes	1076
4909	FR	FR-41	Loir-et-Cher	1076
4910	FR	FR-42	Loire	1076
4911	FR	FR-44	Loire-Atlantique	1076
4912	FR	FR-45	Loiret	1076
4913	FR	FR-46	Lot	1076
4914	FR	FR-47	Lot-et-Garonne	1076
4915	FR	FR-48	Lozère	1076
4916	FR	FR-49	Maine-et-Loire	1076
4917	FR	FR-50	Manche	1076
4918	FR	FR-51	Marne	1076
4919	FR	FR-53	Mayenne	1076
4920	FR	FR-54	Meurthe-et-Moselle	1076
4921	FR	FR-55	Meuse	1076
4922	FR	FR-56	Morbihan	1076
4923	FR	FR-57	Moselle	1076
4924	FR	FR-58	Nièvre	1076
4925	FR	FR-59	Nord	1076
4926	FR	FR-60	Oise	1076
4927	FR	FR-61	Orne	1076
4928	FR	FR-75	Paris	1076
4929	FR	FR-62	Pas-de-Calais	1076
4930	FR	FR-63	Puy-de-Dôme	1076
4931	FR	FR-64	Pyrénées-Atlantiques	1076
4932	FR	FR-66	Pyrénées-Orientales	1076
4933	FR	FR-69	Rhône	1076
4934	FR	FR-71	Saône-et-Loire	1076
4935	FR	FR-72	Sarthe	1076
4936	FR	FR-73	Savoie	1076
4937	FR	FR-77	Seine-et-Marne	1076
4938	FR	FR-76	Seine-Maritime	1076
4939	FR	FR-93	Seine-Saint-Denis	1076
4940	FR	FR-80	Somme	1076
4941	FR	FR-81	Tarn	1076
4942	FR	FR-82	Tarn-et-Garonne	1076
4943	FR	FR-95	Val d'Oise	1076
4944	FR	FR-90	Territoire de Belfort	1076
4945	FR	FR-94	Val-de-Marne	1076
4946	FR	FR-83	Var	1076
4947	FR	FR-84	Vaucluse	1076
4948	FR	FR-85	Vendée	1076
4949	FR	FR-86	Vienne	1076
4950	FR	FR-88	Vosges	1076
4951	FR	FR-89	Yonne	1076
4952	FR	FR-78	Yvelines	1076
4953	GB	GB-ABE	Aberdeen City	1226
4954	GB	GB-ABD	Aberdeenshire	1226
4955	GB	GB-ANS	Angus	1226
4956	GB	GB-ANT	Antrim	1226
4957	GB	GB-ARD	Ards	1226
4958	GB	GB-AGB	Argyll and Bute	1226
4959	GB	GB-ARM	Armagh	1226
4960	GB	GB-BLA	Ballymena	1226
4961	GB	GB-BLY	Ballymoney	1226
4962	GB	GB-BNB	Banbridge	1226
4963	GB	GB-BDG	Barking and Dagenham	1226
4964	GB	GB-BNE	Barnet	1226
4965	GB	GB-BNS	Barnsley	1226
4966	GB	GB-BAS	Bath and North East Somerset	1226
4967	GB	GB-BDF	Bedfordshire	1226
4968	GB	GB-BFS	Belfast	1226
4969	GB	GB-BEX	Bexley	1226
4970	GB	GB-BIR	Birmingham	1226
4971	GB	GB-BBD	Blackburn with Darwen	1226
4972	GB	GB-BPL	Blackpool	1226
4973	GB	GB-BGW	Blaenau Gwent	1226
4974	GB	GB-BOL	Bolton	1226
4975	GB	GB-BMH	Bournemouth	1226
4976	GB	GB-BRC	Bracknell Forest	1226
4977	GB	GB-BRD	Bradford	1226
4978	GB	GB-BEN	Brent	1226
4979	GB	GB-BGE	Bridgend	1226
4980	GB	GB-BNH	Brighton and Hove	1226
4981	GB	GB-BST	Bristol, City of	1226
4982	GB	GB-BRY	Bromley	1226
4983	GB	GB-BKM	Buckinghamshire	1226
4984	GB	GB-BUR	Bury	1226
4985	GB	GB-CAY	Caerphilly	1226
4986	GB	GB-CLD	Calderdale	1226
4987	GB	GB-CAM	Cambridgeshire	1226
4988	GB	GB-CMD	Camden	1226
4989	GB	GB-CRF	Cardiff	1226
4990	GB	GB-CMN	Carmarthenshire	1226
4991	GB	GB-GFY	Sir Gaerfyrddin	1226
4992	GB	GB-CKF	Carrickfergus	1226
4993	GB	GB-CSR	Castlereagh	1226
4994	GB	GB-CGN	Ceredigion	1226
4995	GB	GB-CHS	Cheshire	1226
4996	GB	GB-CLK	Clackmannanshire	1226
4997	GB	GB-CLR	Coleraine	1226
4998	GB	GB-CWY	Conwy	1226
4999	GB	GB-CKT	Cookstown	1226
5000	GB	GB-CON	Cornwall	1226
5001	GB	GB-COV	Coventry	1226
5002	GB	GB-CGV	Cralgavon	1226
5003	GB	GB-CRY	Croydon	1226
5004	GB	GB-CMA	Cumbria	1226
5005	GB	GB-DAL	Darlington	1226
5006	GB	GB-DEN	Denbighshire	1226
5007	GB	GB-DER	Derby	1226
5008	GB	GB-DBY	Derbyshire	1226
5009	GB	GB-DRY	Derry	1226
5010	GB	GB-DEV	Devon	1226
5011	GB	GB-DNC	Doncaster	1226
5012	GB	GB-DOR	Dorset	1226
5013	GB	GB-DOW	Down	1226
5014	GB	GB-DUD	Dudley	1226
5015	GB	GB-DGY	Dumfries and Galloway	1226
5016	GB	GB-DND	Dundee City	1226
5017	GB	GB-DGN	Dungannon	1226
5018	GB	GB-DUR	Durham	1226
5019	GB	GB-EAL	Ealing	1226
5020	GB	GB-EAY	East Ayrshire	1226
5021	GB	GB-EDU	East Dunbartonshire	1226
5022	GB	GB-ELN	East Lothian	1226
5023	GB	GB-ERW	East Renfrewshire	1226
5024	GB	GB-ERY	East Riding of Yorkshire	1226
5025	GB	GB-ESX	East Sussex	1226
5026	GB	GB-EDH	Edinburgh, City of	1226
5027	GB	GB-ELS	Eilean Siar	1226
5028	GB	GB-ENF	Enfield	1226
5029	GB	GB-ESS	Essex	1226
5030	GB	GB-FAL	Falkirk	1226
5031	GB	GB-FER	Fermanagh	1226
5032	GB	GB-FIF	Fife	1226
5033	GB	GB-FLN	Flintshire	1226
5034	GB	GB-GAT	Gateshead	1226
5035	GB	GB-GLG	Glasgow City	1226
5036	GB	GB-GLS	Gloucestershire	1226
5037	GB	GB-GRE	Greenwich	1226
5038	GB	GB-GSY	Guernsey	1226
5039	GB	GB-GWN	Gwynedd	1226
5040	GB	GB-HCK	Hackney	1226
5041	GB	GB-HAL	Halton	1226
5042	GB	GB-HMF	Hammersmith and Fulham	1226
5043	GB	GB-HAM	Hampshire	1226
5044	GB	GB-HRY	Haringey	1226
5045	GB	GB-HRW	Harrow	1226
5046	GB	GB-HPL	Hartlepool	1226
5047	GB	GB-HAV	Havering	1226
5048	GB	GB-HEF	Herefordshire, County of	1226
5049	GB	GB-HRT	Hertfordshire	1226
5050	GB	GB-HED	Highland	1226
5051	GB	GB-HIL	Hillingdon	1226
5052	GB	GB-HNS	Hounslow	1226
5053	GB	GB-IVC	Inverclyde	1226
5054	GB	GB-AGY	Isle of Anglesey	1226
5055	GB	GB-IOW	Isle of Wight	1226
5056	GB	GB-IOS	Isles of Scilly	1226
5057	GB	GB-ISL	Islington	1226
5058	GB	GB-JSY	Jersey	1226
5059	GB	GB-KEC	Kensington and Chelsea	1226
5060	GB	GB-KEN	Kent	1226
5061	GB	GB-KHL	Kingston upon Hull, City of	1226
5062	GB	GB-KTT	Kingston upon Thames	1226
5063	GB	GB-KIR	Kirklees	1226
5064	GB	GB-KWL	Knowsley	1226
5065	GB	GB-LBH	Lambeth	1226
5066	GB	GB-LAN	Lancashire	1226
5067	GB	GB-LRN	Larne	1226
5068	GB	GB-LDS	Leeds	1226
5069	GB	GB-LCE	Leicester	1226
5070	GB	GB-LEC	Leicestershire	1226
5071	GB	GB-LEW	Lewisham	1226
5072	GB	GB-LMV	Limavady	1226
5073	GB	GB-LIN	Lincolnshire	1226
5074	GB	GB-LSB	Lisburn	1226
5075	GB	GB-LIV	Liverpool	1226
5076	GB	GB-LND	London, City of	1226
5077	GB	GB-LUT	Luton	1226
5078	GB	GB-MFT	Magherafelt	1226
5079	GB	GB-MAN	Manchester	1226
5080	GB	GB-MDW	Medway	1226
5081	GB	GB-MTY	Merthyr Tydfil	1226
5082	GB	GB-MRT	Merton	1226
5083	GB	GB-MDB	Middlesbrough	1226
5084	GB	GB-MLN	Midlothian	1226
5085	GB	GB-MIK	Milton Keynes	1226
5086	GB	GB-MON	Monmouthshire	1226
5087	GB	GB-MRY	Moray	1226
5088	GB	GB-MYL	Moyle	1226
5089	GB	GB-NTL	Neath Port Talbot	1226
5090	GB	GB-NET	Newcastle upon Tyne	1226
5091	GB	GB-NWM	Newham	1226
5092	GB	GB-NWP	Newport	1226
5093	GB	GB-NYM	Newry and Mourne	1226
5094	GB	GB-NTA	Newtownabbey	1226
5095	GB	GB-NFK	Norfolk	1226
5096	GB	GB-NAY	North Ayrahire	1226
5097	GB	GB-NDN	North Down	1226
5098	GB	GB-NEL	North East Lincolnshire	1226
5099	GB	GB-NLK	North Lanarkshire	1226
5100	GB	GB-NLN	North Lincolnshire	1226
5101	GB	GB-NSM	North Somerset	1226
5102	GB	GB-NTY	North Tyneside	1226
5103	GB	GB-NYK	North Yorkshire	1226
5104	GB	GB-NTH	Northamptonshire	1226
5105	GB	GB-NBL	Northumbarland	1226
5106	GB	GB-NGM	Nottingham	1226
5107	GB	GB-NTT	Nottinghamshire	1226
5108	GB	GB-OLD	Oldham	1226
5109	GB	GB-OMH	Omagh	1226
5110	GB	GB-ORR	Orkney Islands	1226
5111	GB	GB-OXF	Oxfordshire	1226
5112	GB	GB-PEM	Pembrokeshire	1226
5113	GB	GB-PKN	Perth and Kinross	1226
5114	GB	GB-PTE	Peterborough	1226
5115	GB	GB-PLY	Plymouth	1226
5116	GB	GB-POL	Poole	1226
5117	GB	GB-POR	Portsmouth	1226
5118	GB	GB-POW	Powys	1226
5119	GB	GB-RDG	Reading	1226
5120	GB	GB-RDB	Redbridge	1226
5121	GB	GB-RCC	Redcar and Cleveland	1226
5122	GB	GB-RFW	Renfrewshlre	1226
5123	GB	GB-RCT	Rhondda, Cynon, Taff	1226
5124	GB	GB-RIC	Richmond upon Thames	1226
5125	GB	GB-RCH	Rochdale	1226
5126	GB	GB-ROT	Rotherham	1226
5127	GB	GB-RUT	Rutland	1226
5128	GB	GB-SHN	St. Helens	1226
5129	GB	GB-SLF	Salford	1226
5130	GB	GB-SAW	Sandwell	1226
5131	GB	GB-SCB	Scottish Borders, The	1226
5132	GB	GB-SFT	Sefton	1226
5133	GB	GB-SHF	Sheffield	1226
5134	GB	GB-ZET	Shetland Islands	1226
5135	GB	GB-SHR	Shropshire	1226
5136	GB	GB-SLG	Slough	1226
5137	GB	GB-SOL	Solihull	1226
5138	GB	GB-SOM	merset	1226
5139	GB	GB-SAY	South Ayrshire	1226
5140	GB	GB-SGC	South Gloucestershire	1226
5141	GB	GB-SLK	South Lanarkshire	1226
5142	GB	GB-STY	South Tyneside	1226
5143	GB	GB-STH	Southampton	1226
5144	GB	GB-SOS	Southend-on-Sea	1226
5145	GB	GB-SWK	Southwark	1226
5146	GB	GB-STS	Staffordshire	1226
5147	GB	GB-STG	Stirling	1226
5148	GB	GB-SKP	Stockport	1226
5149	GB	GB-STT	Stockton-on-Tees	1226
5150	GB	GB-STE	Stoke-on-Trent	1226
5151	GB	GB-STB	Strabane	1226
5152	GB	GB-SFK	Suffolk	1226
5153	GB	GB-SND	Sunderland	1226
5154	GB	GB-SRY	Surrey	1226
5155	GB	GB-STN	Sutton	1226
5156	GB	GB-SWA	Swansea	1226
5157	GB	GB-SWD	Swindon	1226
5158	GB	GB-TAM	Tameside	1226
5159	GB	GB-TFW	Telford and Wrekin	1226
5160	GB	GB-THR	Thurrock	1226
5161	GB	GB-TOB	Torbay	1226
5162	GB	GB-TOF	Torfasn	1226
5163	GB	GB-TWH	Tower Hamlets	1226
5164	GB	GB-TRF	Trafford	1226
5165	GB	GB-VGL	Vale of Glamorgan, The	1226
5166	GB	GB-BMG	Bro Morgannwg	1226
5167	GB	GB-WKF	Wakefield	1226
5168	GB	GB-WLL	Walsall	1226
5169	GB	GB-WFT	Waltham Forest	1226
5170	GB	GB-WND	Wandsworth	1226
5171	GB	GB-WRT	Warrington	1226
5172	GB	GB-WAR	Warwickshire	1226
5173	GB	GB-WBX	West Berkshire	1226
5174	GB	GB-WDU	West Dunbartonshire	1226
5175	GB	GB-WLN	West Lothian	1226
5176	GB	GB-WSX	West Sussex	1226
5177	GB	GB-WSM	Westminster	1226
5178	GB	GB-WGN	Wigan	1226
5179	GB	GB-WIL	Wiltshire	1226
5180	GB	GB-WNM	Windsor and Maidenhead	1226
5181	GB	GB-WRL	Wirral	1226
5182	GB	GB-WOK	Wokingham	1226
5183	GB	GB-WLV	Wolverhampton	1226
5184	GB	GB-WOR	Worcestershire	1226
5185	GB	GB-WRX	Wrexham	1226
5186	GB	GB-YOR	York	1226
5187	GH	GH-AH	Ashanti	1083
5188	GH	GH-BA	Brong-Ahafo	1083
5189	GH	GH-AA	Greater Accra	1083
5190	GH	GH-UE	Upper East	1083
5191	GH	GH-UW	Upper West	1083
5192	GH	GH-TV	Volta	1083
5193	GM	GM-B	Banjul	1213
5194	GM	GM-L	Lower River	1213
5195	GM	GM-M	MacCarthy Island	1213
5196	GM	GM-N	North Bank	1213
5197	GM	GM-U	Upper River	1213
5198	GN	GN-BE	Beyla	1091
5199	GN	GN-BF	Boffa	1091
5200	GN	GN-BK	Boke	1091
5201	GN	GN-CO	Coyah	1091
5202	GN	GN-DB	Dabola	1091
5203	GN	GN-DL	Dalaba	1091
5204	GN	GN-DI	Dinguiraye	1091
5205	GN	GN-DU	Dubreka	1091
5206	GN	GN-FA	Faranah	1091
5207	GN	GN-FO	Forecariah	1091
5208	GN	GN-FR	Fria	1091
5209	GN	GN-GA	Gaoual	1091
5210	GN	GN-GU	Guekedou	1091
5211	GN	GN-KA	Kankan	1091
5212	GN	GN-KE	Kerouane	1091
5213	GN	GN-KD	Kindia	1091
5214	GN	GN-KS	Kissidougou	1091
5215	GN	GN-KB	Koubia	1091
5216	GN	GN-KN	Koundara	1091
5217	GN	GN-KO	Kouroussa	1091
5218	GN	GN-LA	Labe	1091
5219	GN	GN-LE	Lelouma	1091
5220	GN	GN-LO	Lola	1091
5221	GN	GN-MC	Macenta	1091
5222	GN	GN-ML	Mali	1091
5223	GN	GN-MM	Mamou	1091
5224	GN	GN-MD	Mandiana	1091
5225	GN	GN-NZ	Nzerekore	1091
5226	GN	GN-PI	Pita	1091
5227	GN	GN-SI	Siguiri	1091
5228	GN	GN-TE	Telimele	1091
5229	GN	GN-TO	Tougue	1091
5230	GN	GN-YO	Yomou	1091
5231	GQ	GQ-C	Region Continental	1067
5232	GQ	GQ-I	Region Insular	1067
5233	GQ	GQ-AN	Annobon	1067
5234	GQ	GQ-BN	Bioko Norte	1067
5235	GQ	GQ-BS	Bioko Sur	1067
5236	GQ	GQ-CS	Centro Sur	1067
5237	GQ	GQ-KN	Kie-Ntem	1067
5238	GQ	GQ-LI	Litoral	1067
5239	GQ	GQ-WN	Wele-Nzas	1067
5240	GR	GR-13	Achaa	1085
5241	GR	GR-01	Aitolia-Akarnania	1085
5242	GR	GR-11	Argolis	1085
5243	GR	GR-12	Arkadia	1085
5244	GR	GR-31	Arta	1085
5245	GR	GR-A1	Attiki	1085
5246	GR	GR-64	Chalkidiki	1085
5247	GR	GR-94	Chania	1085
5248	GR	GR-85	Chios	1085
5249	GR	GR-81	Dodekanisos	1085
5250	GR	GR-52	Drama	1085
5251	GR	GR-71	Evros	1085
5252	GR	GR-05	Evrytania	1085
5253	GR	GR-04	Evvoia	1085
5254	GR	GR-63	Florina	1085
5255	GR	GR-07	Fokis	1085
5256	GR	GR-06	Fthiotis	1085
5257	GR	GR-51	Grevena	1085
5258	GR	GR-14	Ileia	1085
5259	GR	GR-53	Imathia	1085
5260	GR	GR-33	Ioannina	1085
5261	GR	GR-91	Irakleion	1085
5262	GR	GR-41	Karditsa	1085
5263	GR	GR-56	Kastoria	1085
5264	GR	GR-55	Kavalla	1085
5265	GR	GR-23	Kefallinia	1085
5266	GR	GR-22	Kerkyra	1085
5267	GR	GR-57	Kilkis	1085
5268	GR	GR-15	Korinthia	1085
5269	GR	GR-58	Kozani	1085
5270	GR	GR-82	Kyklades	1085
5271	GR	GR-16	Lakonia	1085
5272	GR	GR-42	Larisa	1085
5273	GR	GR-92	Lasithion	1085
5274	GR	GR-24	Lefkas	1085
5275	GR	GR-83	Lesvos	1085
5276	GR	GR-43	Magnisia	1085
5277	GR	GR-17	Messinia	1085
5278	GR	GR-59	Pella	1085
5279	GR	GR-34	Preveza	1085
5280	GR	GR-93	Rethymnon	1085
5281	GR	GR-73	Rodopi	1085
5282	GR	GR-84	Samos	1085
5283	GR	GR-62	Serrai	1085
5284	GR	GR-32	Thesprotia	1085
5285	GR	GR-54	Thessaloniki	1085
5286	GR	GR-44	Trikala	1085
5287	GR	GR-03	Voiotia	1085
5288	GR	GR-72	Xanthi	1085
5289	GR	GR-21	Zakynthos	1085
5290	GR	GR-69	Agio Oros	1085
5291	GT	GT-AV	Alta Verapez	1090
5292	GT	GT-BV	Baja Verapez	1090
5293	GT	GT-CM	Chimaltenango	1090
5294	GT	GT-CQ	Chiquimula	1090
5295	GT	GT-PR	El Progreso	1090
5296	GT	GT-ES	Escuintla	1090
5297	GT	GT-GU	Guatemala	1090
5298	GT	GT-HU	Huehuetenango	1090
5299	GT	GT-IZ	Izabal	1090
5300	GT	GT-JA	Jalapa	1090
5301	GT	GT-JU	Jutiapa	1090
5302	GT	GT-PE	Peten	1090
5303	GT	GT-QZ	Quetzaltenango	1090
5304	GT	GT-QC	Quiche	1090
5305	GT	GT-RE	Reta.thuleu	1090
5306	GT	GT-SA	Sacatepequez	1090
5307	GT	GT-SM	San Marcos	1090
5308	GT	GT-SR	Santa Rosa	1090
5309	GT	GT-SO	Solol6	1090
5310	GT	GT-SU	Suchitepequez	1090
5311	GT	GT-TO	Totonicapan	1090
5312	GT	GT-ZA	Zacapa	1090
5313	GW	GW-BS	Bissau	1092
5314	GW	GW-BA	Bafata	1092
5315	GW	GW-BM	Biombo	1092
5316	GW	GW-BL	Bolama	1092
5317	GW	GW-CA	Cacheu	1092
5318	GW	GW-GA	Gabu	1092
5319	GW	GW-OI	Oio	1092
5320	GW	GW-QU	Quloara	1092
5321	GW	GW-TO	Tombali S	1092
5322	GY	GY-BA	Barima-Waini	1093
5323	GY	GY-CU	Cuyuni-Mazaruni	1093
5324	GY	GY-DE	Demerara-Mahaica	1093
5325	GY	GY-EB	East Berbice-Corentyne	1093
5326	GY	GY-ES	Essequibo Islands-West Demerara	1093
5327	GY	GY-MA	Mahaica-Berbice	1093
5328	GY	GY-PM	Pomeroon-Supenaam	1093
5329	GY	GY-PT	Potaro-Siparuni	1093
5330	GY	GY-UD	Upper Demerara-Berbice	1093
5331	GY	GY-UT	Upper Takutu-Upper Essequibo	1093
5332	HN	HN-AT	Atlantida	1097
5333	HN	HN-CL	Colon	1097
5334	HN	HN-CM	Comayagua	1097
5335	HN	HN-CP	Copan	1097
5336	HN	HN-CR	Cortes	1097
5337	HN	HN-CH	Choluteca	1097
5338	HN	HN-EP	El Paraiso	1097
5339	HN	HN-FM	Francisco Morazan	1097
5340	HN	HN-GD	Gracias a Dios	1097
5341	HN	HN-IN	Intibuca	1097
5342	HN	HN-IB	Islas de la Bahia	1097
5343	HN	HN-LE	Lempira	1097
5344	HN	HN-OC	Ocotepeque	1097
5345	HN	HN-OL	Olancho	1097
5346	HN	HN-SB	Santa Barbara	1097
5347	HN	HN-VA	Valle	1097
5348	HN	HN-YO	Yoro	1097
5349	HR	HR-07	Bjelovarsko-bilogorska zupanija	1055
5350	HR	HR-12	Brodsko-posavska zupanija	1055
5351	HR	HR-19	Dubrovacko-neretvanska zupanija	1055
5352	HR	HR-18	Istarska zupanija	1055
5353	HR	HR-04	Karlovacka zupanija	1055
5354	HR	HR-06	Koprivnickco-krizevacka zupanija	1055
5355	HR	HR-02	Krapinako-zagorska zupanija	1055
5356	HR	HR-09	Licko-senjska zupanija	1055
5357	HR	HR-20	Medimurska zupanija	1055
5358	HR	HR-14	Osjecko-baranjska zupanija	1055
5359	HR	HR-11	Pozesko-slavonska zupanija	1055
5360	HR	HR-08	Primorsko-goranska zupanija	1055
5361	HR	HR-03	Sisacko-moelavacka Iupanija	1055
5362	HR	HR-17	Splitako-dalmatinska zupanija	1055
5363	HR	HR-15	Sibenako-kninska zupanija	1055
5364	HR	HR-05	Varaidinska zupanija	1055
5365	HR	HR-10	VirovitiEko-podravska zupanija	1055
5366	HR	HR-16	VuRovarako-srijemska zupanija	1055
5367	HR	HR-13	Zadaraka	1055
5368	HR	HR-01	Zagrebacka zupanija	1055
5369	HT	HT-GA	Grande-Anse	1094
5370	HT	HT-NE	Nord-Eat	1094
5371	HT	HT-NO	Nord-Ouest	1094
5372	HT	HT-OU	Ouest	1094
5373	HT	HT-SD	Sud	1094
5374	HT	HT-SE	Sud-Est	1094
5375	HU	HU-BU	Budapest	1099
5376	HU	HU-BK	Bács-Kiskun	1099
5377	HU	HU-BA	Baranya	1099
5378	HU	HU-BE	Békés	1099
5379	HU	HU-BZ	Borsod-Abaúj-Zemplén	1099
5380	HU	HU-CS	Csongrád	1099
5381	HU	HU-FE	Fejér	1099
5382	HU	HU-GS	Győr-Moson-Sopron	1099
5383	HU	HU-HB	Hajdu-Bihar	1099
5384	HU	HU-HE	Heves	1099
5385	HU	HU-JN	Jász-Nagykun-Szolnok	1099
5386	HU	HU-KE	Komárom-Esztergom	1099
5387	HU	HU-NO	Nográd	1099
5388	HU	HU-PE	Pest	1099
5389	HU	HU-SO	Somogy	1099
5390	HU	HU-SZ	Szabolcs-Szatmár-Bereg	1099
5391	HU	HU-TO	Tolna	1099
5392	HU	HU-VA	Vas	1099
5393	HU	HU-VE	Veszprém	1099
5394	HU	HU-ZA	Zala	1099
5395	HU	HU-BC	Békéscsaba	1099
5396	HU	HU-DE	Debrecen	1099
5397	HU	HU-DU	Dunaújváros	1099
5398	HU	HU-EG	Eger	1099
5399	HU	HU-GY	Győr	1099
5400	HU	HU-HV	Hódmezővásárhely	1099
5401	HU	HU-KV	Kaposvár	1099
5402	HU	HU-KM	Kecskemét	1099
5403	HU	HU-MI	Miskolc	1099
5404	HU	HU-NK	Nagykanizsa	1099
5405	HU	HU-NY	Nyiregyháza	1099
5406	HU	HU-PS	Pécs	1099
5407	HU	HU-ST	Salgótarján	1099
5408	HU	HU-SN	Sopron	1099
5409	HU	HU-SD	Szeged	1099
5410	HU	HU-SF	Székesfehérvár	1099
5411	HU	HU-SS	Szekszárd	1099
5412	HU	HU-SK	Szolnok	1099
5413	HU	HU-SH	Szombathely	1099
5414	HU	HU-TB	Tatabánya	1099
5415	HU	HU-ZE	Zalaegerszeg	1099
5416	ID	ID-BA	Bali	1102
5417	ID	ID-BB	Bangka Belitung	1102
5418	ID	ID-BT	Banten	1102
5419	ID	ID-BE	Bengkulu	1102
5420	ID	ID-GO	Gorontalo	1102
5421	ID	ID-IJ	Irian Jaya	1102
5422	ID	ID-JA	Jambi	1102
5423	ID	ID-JB	Jawa Barat	1102
5424	ID	ID-JT	Jawa Tengah	1102
5425	ID	ID-JI	Jawa Timur	1102
5426	ID	ID-KB	Kalimantan Barat	1102
5427	ID	ID-KT	Kalimantan Timur	1102
5428	ID	ID-KS	Kalimantan Selatan	1102
5429	ID	ID-KR	Kepulauan Riau	1102
5430	ID	ID-LA	Lampung	1102
5431	ID	ID-MA	Maluku	1102
5432	ID	ID-MU	Maluku Utara	1102
5433	ID	ID-NB	Nusa Tenggara Barat	1102
5434	ID	ID-NT	Nusa Tenggara Timur	1102
5435	ID	ID-PA	Papua	1102
5436	ID	ID-RI	Riau	1102
5437	ID	ID-SN	Sulawesi Selatan	1102
5438	ID	ID-ST	Sulawesi Tengah	1102
5439	ID	ID-SG	Sulawesi Tenggara	1102
5440	ID	ID-SA	Sulawesi Utara	1102
5441	ID	ID-SB	Sumatra Barat	1102
5442	ID	ID-SS	Sumatra Selatan	1102
5443	ID	ID-SU	Sumatera Utara	1102
5444	ID	ID-JK	Jakarta Raya	1102
5445	ID	ID-AC	Aceh	1102
5446	ID	ID-YO	Yogyakarta	1102
5447	IE	IE-C	Cork	1105
5448	IE	IE-CE	Clare	1105
5449	IE	IE-CN	Cavan	1105
5450	IE	IE-CW	Carlow	1105
5451	IE	IE-D	Dublin	1105
5452	IE	IE-DL	Donegal	1105
5453	IE	IE-G	Galway	1105
5454	IE	IE-KE	Kildare	1105
5455	IE	IE-KK	Kilkenny	1105
5456	IE	IE-KY	Kerry	1105
5457	IE	IE-LD	Longford	1105
5458	IE	IE-LH	Louth	1105
5459	IE	IE-LK	Limerick	1105
5460	IE	IE-LM	Leitrim	1105
5461	IE	IE-LS	Laois	1105
5462	IE	IE-MH	Meath	1105
5463	IE	IE-MN	Monaghan	1105
5464	IE	IE-MO	Mayo	1105
5465	IE	IE-OY	Offaly	1105
5466	IE	IE-RN	Roscommon	1105
5467	IE	IE-SO	Sligo	1105
5468	IE	IE-TA	Tipperary	1105
5469	IE	IE-WD	Waterford	1105
5470	IE	IE-WH	Westmeath	1105
5471	IE	IE-WW	Wicklow	1105
5472	IE	IE-WX	Wexford	1105
5473	IL	IL-D	HaDarom	1106
5474	IL	IL-M	HaMerkaz	1106
5475	IL	IL-Z	HaZafon	1106
5476	IL	IL-HA	Hefa	1106
5477	IL	IL-TA	Tel-Aviv	1106
5478	IL	IL-JM	Yerushalayim Al Quds	1106
5479	IN	IN-AP	Andhra Pradesh	1101
5480	IN	IN-AR	Arunachal Pradesh	1101
5481	IN	IN-AS	Assam	1101
5482	IN	IN-BR	Bihar	1101
5483	IN	IN-CH	Chhattisgarh	1101
5484	IN	IN-GA	Goa	1101
5485	IN	IN-GJ	Gujarat	1101
5486	IN	IN-HR	Haryana	1101
5487	IN	IN-HP	Himachal Pradesh	1101
5488	IN	IN-JK	Jammu and Kashmir	1101
5489	IN	IN-JH	Jharkhand	1101
5490	IN	IN-KA	Karnataka	1101
5491	IN	IN-KL	Kerala	1101
5492	IN	IN-MP	Madhya Pradesh	1101
5493	IN	IN-MM	Maharashtra	1101
5494	IN	IN-MN	Manipur	1101
5495	IN	IN-ML	Meghalaya	1101
5496	IN	IN-MZ	Mizoram	1101
5497	IN	IN-NL	Nagaland	1101
5498	IN	IN-OR	Orissa	1101
5499	IN	IN-PB	Punjab	1101
5500	IN	IN-RJ	Rajasthan	1101
5501	IN	IN-SK	Sikkim	1101
5502	IN	IN-TN	Tamil Nadu	1101
5503	IN	IN-TR	Tripura	1101
5504	IN	IN-UL	Uttaranchal	1101
5505	IN	IN-UP	Uttar Pradesh	1101
5506	IN	IN-WB	West Bengal	1101
5507	IN	IN-AN	Andaman and Nicobar Islands	1101
5508	IN	IN-DN	Dadra and Nagar Haveli	1101
5509	IN	IN-DD	Daman and Diu	1101
5510	IN	IN-DL	Delhi	1101
5511	IN	IN-LD	Lakshadweep	1101
5512	IN	IN-PY	Pondicherry	1101
5513	IQ	IQ-AN	Al Anbar	1104
5514	IQ	IQ-BA	Al Ba,rah	1104
5515	IQ	IQ-MU	Al Muthanna	1104
5516	IQ	IQ-QA	Al Qadisiyah	1104
5517	IQ	IQ-NA	An Najef	1104
5518	IQ	IQ-AR	Arbil	1104
5519	IQ	IQ-SW	As Sulaymaniyah	1104
5520	IQ	IQ-TS	At Ta'mim	1104
5521	IQ	IQ-BB	Babil	1104
5522	IQ	IQ-BG	Baghdad	1104
5523	IQ	IQ-DA	Dahuk	1104
5524	IQ	IQ-DQ	Dhi Qar	1104
5525	IQ	IQ-DI	Diyala	1104
5526	IQ	IQ-KA	Karbala'	1104
5527	IQ	IQ-MA	Maysan	1104
5528	IQ	IQ-NI	Ninawa	1104
5529	IQ	IQ-SD	Salah ad Din	1104
5530	IQ	IQ-WA	Wasit	1104
5531	IR	IR-03	Ardabil	1103
5532	IR	IR-02	Azarbayjan-e Gharbi	1103
5533	IR	IR-01	Azarbayjan-e Sharqi	1103
5534	IR	IR-06	Bushehr	1103
5535	IR	IR-08	Chahar Mahall va Bakhtiari	1103
5536	IR	IR-04	Esfahan	1103
5537	IR	IR-14	Fars	1103
5538	IR	IR-19	Gilan	1103
5539	IR	IR-27	Golestan	1103
5540	IR	IR-24	Hamadan	1103
5541	IR	IR-23	Hormozgan	1103
5542	IR	IR-05	Iiam	1103
5543	IR	IR-15	Kerman	1103
5544	IR	IR-17	Kermanshah	1103
5545	IR	IR-09	Khorasan	1103
5546	IR	IR-10	Khuzestan	1103
5547	IR	IR-18	Kohjiluyeh va Buyer Ahmad	1103
5548	IR	IR-16	Kordestan	1103
5549	IR	IR-20	Lorestan	1103
5550	IR	IR-22	Markazi	1103
5551	IR	IR-21	Mazandaran	1103
5552	IR	IR-28	Qazvin	1103
5553	IR	IR-26	Qom	1103
5554	IR	IR-12	Semnan	1103
5555	IR	IR-13	Sistan va Baluchestan	1103
5556	IR	IR-07	Tehran	1103
5557	IR	IR-25	Yazd	1103
5558	IR	IR-11	Zanjan	1103
5559	IS	IS-7	Austurland	1100
5560	IS	IS-1	Hofuoborgarsvaeoi utan Reykjavikur	1100
5561	IS	IS-6	Norourland eystra	1100
5562	IS	IS-5	Norourland vestra	1100
5563	IS	IS-0	Reykjavik	1100
5564	IS	IS-8	Suourland	1100
5565	IS	IS-2	Suournes	1100
5566	IS	IS-4	Vestfirolr	1100
5567	IS	IS-3	Vesturland	1100
5568	IT	IT-AG	Agrigento	1107
5569	IT	IT-AL	Alessandria	1107
5570	IT	IT-AN	Ancona	1107
5571	IT	IT-AO	Aosta	1107
5572	IT	IT-AR	Arezzo	1107
5573	IT	IT-AP	Ascoli Piceno	1107
5574	IT	IT-AT	Asti	1107
5575	IT	IT-AV	Avellino	1107
5576	IT	IT-BA	Bari	1107
5577	IT	IT-BL	Belluno	1107
5578	IT	IT-BN	Benevento	1107
5579	IT	IT-BG	Bergamo	1107
5580	IT	IT-BI	Biella	1107
5581	IT	IT-BO	Bologna	1107
5582	IT	IT-BZ	Bolzano	1107
5583	IT	IT-BS	Brescia	1107
5584	IT	IT-BR	Brindisi	1107
5585	IT	IT-CA	Cagliari	1107
5586	IT	IT-CL	Caltanissetta	1107
5587	IT	IT-CB	Campobasso	1107
5588	IT	IT-CE	Caserta	1107
5589	IT	IT-CT	Catania	1107
5590	IT	IT-CZ	Catanzaro	1107
5591	IT	IT-CH	Chieti	1107
5592	IT	IT-CO	Como	1107
5593	IT	IT-CS	Cosenza	1107
5594	IT	IT-CR	Cremona	1107
5595	IT	IT-KR	Crotone	1107
5596	IT	IT-CN	Cuneo	1107
5597	IT	IT-EN	Enna	1107
5598	IT	IT-FE	Ferrara	1107
5599	IT	IT-FI	Firenze	1107
5600	IT	IT-FG	Foggia	1107
5601	IT	IT-FO	Forli	1107
5602	IT	IT-FR	Frosinone	1107
5603	IT	IT-GE	Genova	1107
5604	IT	IT-GO	Gorizia	1107
5605	IT	IT-GR	Grosseto	1107
5606	IT	IT-IM	Imperia	1107
5607	IT	IT-IS	Isernia	1107
5608	IT	IT-AQ	L'Aquila	1107
5609	IT	IT-SP	La Spezia	1107
5610	IT	IT-LT	Latina	1107
5611	IT	IT-LE	Lecce	1107
5612	IT	IT-LC	Lecco	1107
5613	IT	IT-LI	Livorno	1107
5614	IT	IT-LO	Lodi	1107
5615	IT	IT-LU	Lucca	1107
5616	IT	IT-SC	Macerata	1107
5617	IT	IT-MN	Mantova	1107
5618	IT	IT-MS	Massa-Carrara	1107
5619	IT	IT-MT	Matera	1107
5620	IT	IT-ME	Messina	1107
5621	IT	IT-MI	Milano	1107
5622	IT	IT-MO	Modena	1107
5623	IT	IT-NA	Napoli	1107
5624	IT	IT-NO	Novara	1107
5625	IT	IT-NU	Nuoro	1107
5626	IT	IT-OR	Oristano	1107
5627	IT	IT-PD	Padova	1107
5628	IT	IT-PA	Palermo	1107
5629	IT	IT-PR	Parma	1107
5630	IT	IT-PV	Pavia	1107
5631	IT	IT-PG	Perugia	1107
5632	IT	IT-PS	Pesaro e Urbino	1107
5633	IT	IT-PE	Pescara	1107
5634	IT	IT-PC	Piacenza	1107
5635	IT	IT-PI	Pisa	1107
5636	IT	IT-PT	Pistoia	1107
5637	IT	IT-PN	Pordenone	1107
5638	IT	IT-PZ	Potenza	1107
5639	IT	IT-PO	Prato	1107
5640	IT	IT-RG	Ragusa	1107
5641	IT	IT-RA	Ravenna	1107
5642	IT	IT-RC	Reggio Calabria	1107
5643	IT	IT-RE	Reggio Emilia	1107
5644	IT	IT-RI	Rieti	1107
5645	IT	IT-RN	Rimini	1107
5646	IT	IT-RM	Roma	1107
5647	IT	IT-RO	Rovigo	1107
5648	IT	IT-SA	Salerno	1107
5649	IT	IT-SS	Sassari	1107
5650	IT	IT-SV	Savona	1107
5651	IT	IT-SI	Siena	1107
5652	IT	IT-SR	Siracusa	1107
5653	IT	IT-SO	Sondrio	1107
5654	IT	IT-TA	Taranto	1107
5655	IT	IT-TE	Teramo	1107
5656	IT	IT-TR	Terni	1107
5657	IT	IT-TO	Torino	1107
5658	IT	IT-TP	Trapani	1107
5659	IT	IT-TN	Trento	1107
5660	IT	IT-TV	Treviso	1107
5661	IT	IT-TS	Trieste	1107
5662	IT	IT-UD	Udine	1107
5663	IT	IT-VA	Varese	1107
5664	IT	IT-VE	Venezia	1107
5665	IT	IT-VB	Verbano-Cusio-Ossola	1107
5666	IT	IT-VC	Vercelli	1107
5667	IT	IT-VR	Verona	1107
5668	IT	IT-VV	Vibo Valentia	1107
5669	IT	IT-VI	Vicenza	1107
5670	IT	IT-VT	Viterbo	1107
5671	JP	JP-23	Aichi	1109
5672	JP	JP-05	Akita	1109
5673	JP	JP-02	Aomori	1109
5674	JP	JP-12	Chiba	1109
5675	JP	JP-38	Ehime	1109
5676	JP	JP-18	Fukui	1109
5677	JP	JP-40	Fukuoka	1109
5678	JP	JP-07	Fukusima	1109
5679	JP	JP-21	Gifu	1109
5680	JP	JP-10	Gunma	1109
5681	JP	JP-34	Hiroshima	1109
5682	JP	JP-01	Hokkaido	1109
5683	JP	JP-28	Hyogo	1109
5684	JP	JP-08	Ibaraki	1109
5685	JP	JP-17	Ishikawa	1109
5686	JP	JP-03	Iwate	1109
5687	JP	JP-37	Kagawa	1109
5688	JP	JP-46	Kagoshima	1109
5689	JP	JP-14	Kanagawa	1109
5690	JP	JP-39	Kochi	1109
5691	JP	JP-43	Kumamoto	1109
5692	JP	JP-26	Kyoto	1109
5693	JP	JP-24	Mie	1109
5694	JP	JP-04	Miyagi	1109
5695	JP	JP-45	Miyazaki	1109
5696	JP	JP-20	Nagano	1109
5697	JP	JP-42	Nagasaki	1109
5698	JP	JP-29	Nara	1109
5699	JP	JP-15	Niigata	1109
5700	JP	JP-44	Oita	1109
5701	JP	JP-33	Okayama	1109
5702	JP	JP-47	Okinawa	1109
5703	JP	JP-27	Osaka	1109
5704	JP	JP-41	Saga	1109
5705	JP	JP-11	Saitama	1109
5706	JP	JP-25	Shiga	1109
5707	JP	JP-32	Shimane	1109
5708	JP	JP-22	Shizuoka	1109
5709	JP	JP-09	Tochigi	1109
5710	JP	JP-36	Tokushima	1109
5711	JP	JP-13	Tokyo	1109
5712	JP	JP-31	Tottori	1109
5713	JP	JP-16	Toyama	1109
5714	JP	JP-30	Wakayama	1109
5715	JP	JP-06	Yamagata	1109
5716	JP	JP-35	Yamaguchi	1109
5717	JP	JP-19	Yamanashi	1109
5718	JM	JM-13	Clarendon	1108
5719	JM	JM-09	Hanover	1108
5720	JM	JM-01	Kingston	1108
5721	JM	JM-04	Portland	1108
5722	JM	JM-02	Saint Andrew	1108
5723	JM	JM-06	Saint Ann	1108
5724	JM	JM-14	Saint Catherine	1108
5725	JM	JM-11	Saint Elizabeth	1108
5726	JM	JM-08	Saint James	1108
5727	JM	JM-05	Saint Mary	1108
5728	JM	JM-03	Saint Thomea	1108
5729	JM	JM-07	Trelawny	1108
5730	JM	JM-10	Westmoreland	1108
5731	JO	JO-AJ	Ajln	1110
5732	JO	JO-AQ	Al 'Aqaba	1110
5733	JO	JO-BA	Al Balqa'	1110
5734	JO	JO-KA	Al Karak	1110
5735	JO	JO-MA	Al Mafraq	1110
5736	JO	JO-AM	Amman	1110
5737	JO	JO-AT	At Tafilah	1110
5738	JO	JO-AZ	Az Zarga	1110
5739	JO	JO-JR	Irbid	1110
5740	JO	JO-JA	Jarash	1110
5741	JO	JO-MN	Ma'an	1110
5742	JO	JO-MD	Madaba	1110
5743	KE	KE-110	Nairobi Municipality	1112
5744	KE	KE-300	Coast	1112
5745	KE	KE-500	North-Eastern Kaskazini Mashariki	1112
5746	KE	KE-700	Rift Valley	1112
5747	KE	KE-900	Western Magharibi	1112
5748	KG	KG-GB	Bishkek	1117
5749	KG	KG-B	Batken	1117
5750	KG	KG-C	Chu	1117
5751	KG	KG-J	Jalal-Abad	1117
5752	KG	KG-N	Naryn	1117
5753	KG	KG-O	Osh	1117
5754	KG	KG-T	Talas	1117
5755	KG	KG-Y	Ysyk-Kol	1117
5756	KH	KH-23	Krong Kaeb	1037
5757	KH	KH-24	Krong Pailin	1037
5758	KH	KH-18	Xrong Preah Sihanouk	1037
5759	KH	KH-12	Phnom Penh	1037
5760	KH	KH-2	Baat Dambang	1037
5761	KH	KH-1	Banteay Mean Chey	1037
5762	KH	KH-3	Rampong Chaam	1037
5763	KH	KH-4	Kampong Chhnang	1037
5764	KH	KH-5	Kampong Spueu	1037
5765	KH	KH-6	Kampong Thum	1037
5766	KH	KH-7	Kampot	1037
5767	KH	KH-8	Kandaal	1037
5768	KH	KH-9	Kach Kong	1037
5769	KH	KH-10	Krachoh	1037
5770	KH	KH-11	Mondol Kiri	1037
5771	KH	KH-22	Otdar Mean Chey	1037
5772	KH	KH-15	Pousaat	1037
5773	KH	KH-13	Preah Vihear	1037
5774	KH	KH-14	Prey Veaeng	1037
5775	KH	KH-16	Rotanak Kiri	1037
5776	KH	KH-17	Siem Reab	1037
5777	KH	KH-19	Stueng Traeng	1037
5778	KH	KH-20	Svaay Rieng	1037
5779	KH	KH-21	Taakaev	1037
5780	KI	KI-G	Gilbert Islands	1113
5781	KI	KI-L	Line Islands	1113
5782	KI	KI-P	Phoenix Islands	1113
5783	KM	KM-A	Anjouan Ndzouani	1049
5784	KM	KM-G	Grande Comore Ngazidja	1049
5785	KM	KM-M	Moheli Moili	1049
5786	KP	KP-KAE	Kaesong-si	1114
5787	KP	KP-NAM	Nampo-si	1114
5788	KP	KP-PYO	Pyongyang-ai	1114
5789	KP	KP-CHA	Chagang-do	1114
5790	KP	KP-HAB	Hamgyongbuk-do	1114
5791	KP	KP-HAN	Hamgyongnam-do	1114
5792	KP	KP-HWB	Hwanghaebuk-do	1114
5793	KP	KP-HWN	Hwanghaenam-do	1114
5794	KP	KP-KAN	Kangwon-do	1114
5795	KP	KP-PYB	Pyonganbuk-do	1114
5796	KP	KP-PYN	Pyongannam-do	1114
5797	KP	KP-YAN	Yanggang-do	1114
5798	KP	KP-NAJ	Najin Sonbong-si	1114
5799	KR	KR-11	Seoul Teugbyeolsi	1115
5800	KR	KR-26	Busan Gwang'yeogsi	1115
5801	KR	KR-27	Daegu Gwang'yeogsi	1115
5802	KR	KR-30	Daejeon Gwang'yeogsi	1115
5803	KR	KR-29	Gwangju Gwang'yeogsi	1115
5804	KR	KR-28	Incheon Gwang'yeogsi	1115
5805	KR	KR-31	Ulsan Gwang'yeogsi	1115
5806	KR	KR-43	Chungcheongbugdo	1115
5807	KR	KR-44	Chungcheongnamdo	1115
5808	KR	KR-42	Gang'weondo	1115
5809	KR	KR-41	Gyeonggido	1115
5810	KR	KR-47	Gyeongsangbugdo	1115
5811	KR	KR-48	Gyeongsangnamdo	1115
5812	KR	KR-49	Jejudo	1115
5813	KR	KR-45	Jeonrabugdo	1115
5814	KR	KR-46	Jeonranamdo	1115
5815	KW	KW-AH	Al Ahmadi	1116
5816	KW	KW-FA	Al Farwanlyah	1116
5817	KW	KW-JA	Al Jahrah	1116
5818	KW	KW-KU	Al Kuwayt	1116
5819	KW	KW-HA	Hawalli	1116
5820	KZ	KZ-ALA	Almaty	1111
5821	KZ	KZ-AST	Astana	1111
5822	KZ	KZ-ALM	Almaty oblysy	1111
5823	KZ	KZ-AKM	Aqmola oblysy	1111
5824	KZ	KZ-AKT	Aqtobe oblysy	1111
5825	KZ	KZ-ATY	Atyrau oblyfiy	1111
5826	KZ	KZ-ZAP	Batys Quzaqstan oblysy	1111
5827	KZ	KZ-MAN	Mangghystau oblysy	1111
5828	KZ	KZ-YUZ	Ongtustik Quzaqstan oblysy	1111
5829	KZ	KZ-PAV	Pavlodar oblysy	1111
5830	KZ	KZ-KAR	Qaraghandy oblysy	1111
5831	KZ	KZ-KUS	Qostanay oblysy	1111
5832	KZ	KZ-KZY	Qyzylorda oblysy	1111
5833	KZ	KZ-VOS	Shyghys Quzaqstan oblysy	1111
5834	KZ	KZ-SEV	Soltustik Quzaqstan oblysy	1111
5835	KZ	KZ-ZHA	Zhambyl oblysy Zhambylskaya oblast'	1111
5836	LA	LA-VT	Vientiane	1118
5837	LA	LA-AT	Attapu	1118
5838	LA	LA-BK	Bokeo	1118
5839	LA	LA-BL	Bolikhamxai	1118
5840	LA	LA-CH	Champasak	1118
5841	LA	LA-HO	Houaphan	1118
5842	LA	LA-KH	Khammouan	1118
5843	LA	LA-LM	Louang Namtha	1118
5844	LA	LA-LP	Louangphabang	1118
5845	LA	LA-OU	Oudomxai	1118
5846	LA	LA-PH	Phongsali	1118
5847	LA	LA-SL	Salavan	1118
5848	LA	LA-SV	Savannakhet	1118
5849	LA	LA-XA	Xaignabouli	1118
5850	LA	LA-XN	Xiasomboun	1118
5851	LA	LA-XE	Xekong	1118
5852	LA	LA-XI	Xiangkhoang	1118
5853	LB	LB-BA	Beirout	1120
5854	LB	LB-BI	El Begsa	1120
5855	LB	LB-JL	Jabal Loubnane	1120
5856	LB	LB-AS	Loubnane ech Chemali	1120
5857	LB	LB-JA	Loubnane ej Jnoubi	1120
5858	LB	LB-NA	Nabatiye	1120
5859	LK	LK-52	Ampara	1199
5860	LK	LK-71	Anuradhapura	1199
5861	LK	LK-81	Badulla	1199
5862	LK	LK-51	Batticaloa	1199
5863	LK	LK-11	Colombo	1199
5864	LK	LK-31	Galle	1199
5865	LK	LK-12	Gampaha	1199
5866	LK	LK-33	Hambantota	1199
5867	LK	LK-41	Jaffna	1199
5868	LK	LK-13	Kalutara	1199
5869	LK	LK-21	Kandy	1199
5870	LK	LK-92	Kegalla	1199
5871	LK	LK-42	Kilinochchi	1199
5872	LK	LK-61	Kurunegala	1199
5873	LK	LK-43	Mannar	1199
5874	LK	LK-22	Matale	1199
5875	LK	LK-32	Matara	1199
5876	LK	LK-82	Monaragala	1199
5877	LK	LK-45	Mullaittivu	1199
5878	LK	LK-23	Nuwara Eliya	1199
5879	LK	LK-72	Polonnaruwa	1199
5880	LK	LK-62	Puttalum	1199
5881	LK	LK-91	Ratnapura	1199
5882	LK	LK-53	Trincomalee	1199
5883	LK	LK-44	VavunLya	1199
5884	LR	LR-BM	Bomi	1122
5885	LR	LR-BG	Bong	1122
5886	LR	LR-GB	Grand Basaa	1122
5887	LR	LR-CM	Grand Cape Mount	1122
5888	LR	LR-GG	Grand Gedeh	1122
5889	LR	LR-GK	Grand Kru	1122
5890	LR	LR-LO	Lofa	1122
5891	LR	LR-MG	Margibi	1122
5892	LR	LR-MY	Maryland	1122
5893	LR	LR-MO	Montserrado	1122
5894	LR	LR-NI	Nimba	1122
5895	LR	LR-RI	Rivercess	1122
5896	LR	LR-SI	Sinoe	1122
5897	LS	LS-D	Berea	1121
5898	LS	LS-B	Butha-Buthe	1121
5899	LS	LS-C	Leribe	1121
5900	LS	LS-E	Mafeteng	1121
5901	LS	LS-A	Maseru	1121
5902	LS	LS-F	Mohale's Hoek	1121
5903	LS	LS-J	Mokhotlong	1121
5904	LS	LS-H	Qacha's Nek	1121
5905	LS	LS-G	Quthing	1121
5906	LS	LS-K	Thaba-Tseka	1121
5907	LT	LT-AL	Alytaus Apskritis	1125
5908	LT	LT-KU	Kauno Apskritis	1125
5909	LT	LT-KL	Klaipedos Apskritis	1125
5910	LT	LT-MR	Marijampoles Apskritis	1125
5911	LT	LT-PN	Panevezio Apskritis	1125
5912	LT	LT-SA	Sisuliu Apskritis	1125
5913	LT	LT-TA	Taurages Apskritis	1125
5914	LT	LT-TE	Telsiu Apskritis	1125
5915	LT	LT-UT	Utenos Apskritis	1125
5916	LT	LT-VL	Vilniaus Apskritis	1125
5917	LU	LU-D	Diekirch	1126
5918	LU	LU-G	GreveNmacher	1126
5919	LV	LV-AI	Aizkraukles Apripkis	1119
5920	LV	LV-AL	Alkanes Apripkis	1119
5921	LV	LV-BL	Balvu Apripkis	1119
5922	LV	LV-BU	Bauskas Apripkis	1119
5923	LV	LV-CE	Cesu Aprikis	1119
5924	LV	LV-DA	Daugavpile Apripkis	1119
5925	LV	LV-DO	Dobeles Apripkis	1119
5926	LV	LV-GU	Gulbenes Aprlpkis	1119
5927	LV	LV-JL	Jelgavas Apripkis	1119
5928	LV	LV-JK	Jekabpils Apripkis	1119
5929	LV	LV-KR	Kraslavas Apripkis	1119
5930	LV	LV-KU	Kuldlgas Apripkis	1119
5931	LV	LV-LM	Limbazu Apripkis	1119
5932	LV	LV-LE	Liepajas Apripkis	1119
5933	LV	LV-LU	Ludzas Apripkis	1119
5934	LV	LV-MA	Madonas Apripkis	1119
5935	LV	LV-OG	Ogres Apripkis	1119
5936	LV	LV-PR	Preilu Apripkis	1119
5937	LV	LV-RE	Rezaknes Apripkis	1119
5938	LV	LV-RI	Rigas Apripkis	1119
5939	LV	LV-SA	Saldus Apripkis	1119
5940	LV	LV-TA	Talsu Apripkis	1119
5941	LV	LV-TU	Tukuma Apriplcis	1119
5942	LV	LV-VK	Valkas Apripkis	1119
5943	LV	LV-VM	Valmieras Apripkis	1119
5944	LV	LV-VE	Ventspils Apripkis	1119
5945	LV	LV-DGV	Daugavpils	1119
5946	LV	LV-JEL	Jelgava	1119
5947	LV	LV-JUR	Jurmala	1119
5948	LV	LV-LPX	Liepaja	1119
5949	LV	LV-REZ	Rezekne	1119
5950	LV	LV-RIX	Riga	1119
5951	LV	LV-VEN	Ventspils	1119
5952	LY	LY-AJ	Ajdābiyā	1123
5953	LY	LY-BU	Al Buţnān	1123
5954	LY	LY-HZ	Al Hizām al Akhdar	1123
5955	LY	LY-JA	Al Jabal al Akhdar	1123
5956	LY	LY-JI	Al Jifārah	1123
5957	LY	LY-JU	Al Jufrah	1123
5958	LY	LY-KF	Al Kufrah	1123
5959	LY	LY-MJ	Al Marj	1123
5960	LY	LY-MB	Al Marqab	1123
5961	LY	LY-QT	Al Qaţrūn	1123
5962	LY	LY-QB	Al Qubbah	1123
5963	LY	LY-WA	Al Wāhah	1123
5964	LY	LY-NQ	An Nuqaţ al Khams	1123
5965	LY	LY-SH	Ash Shāţi'	1123
5966	LY	LY-ZA	Az Zāwiyah	1123
5967	LY	LY-BA	Banghāzī	1123
5968	LY	LY-BW	Banī Walīd	1123
5969	LY	LY-DR	Darnah	1123
5970	LY	LY-GD	Ghadāmis	1123
5971	LY	LY-GR	Gharyān	1123
5972	LY	LY-GT	Ghāt	1123
5973	LY	LY-JB	Jaghbūb	1123
5974	LY	LY-MI	Mişrātah	1123
5975	LY	LY-MZ	Mizdah	1123
5976	LY	LY-MQ	Murzuq	1123
5977	LY	LY-NL	Nālūt	1123
5978	LY	LY-SB	Sabhā	1123
5979	LY	LY-SS	Şabrātah Şurmān	1123
5980	LY	LY-SR	Surt	1123
5981	LY	LY-TN	Tājūrā' wa an Nawāhī al Arbāh	1123
5982	LY	LY-TB	Ţarābulus	1123
5983	LY	LY-TM	Tarhūnah-Masallātah	1123
5984	LY	LY-WD	Wādī al hayāt	1123
5985	LY	LY-YJ	Yafran-Jādū	1123
5986	MA	MA-AGD	Agadir	1146
5987	MA	MA-BAH	Aït Baha	1146
5988	MA	MA-MEL	Aït Melloul	1146
5989	MA	MA-HAO	Al Haouz	1146
5990	MA	MA-HOC	Al Hoceïma	1146
5991	MA	MA-ASZ	Assa-Zag	1146
5992	MA	MA-AZI	Azilal	1146
5993	MA	MA-BEM	Beni Mellal	1146
5994	MA	MA-BES	Ben Sllmane	1146
5995	MA	MA-BER	Berkane	1146
5996	MA	MA-BOD	Boujdour	1146
5997	MA	MA-BOM	Boulemane	1146
5998	MA	MA-CAS	Casablanca  [Dar el Beïda]	1146
5999	MA	MA-CHE	Chefchaouene	1146
6000	MA	MA-CHI	Chichaoua	1146
6001	MA	MA-HAJ	El Hajeb	1146
6002	MA	MA-JDI	El Jadida	1146
6003	MA	MA-ERR	Errachidia	1146
6004	MA	MA-ESI	Essaouira	1146
6005	MA	MA-ESM	Es Smara	1146
6006	MA	MA-FES	Fès	1146
6007	MA	MA-FIG	Figuig	1146
6008	MA	MA-GUE	Guelmim	1146
6009	MA	MA-IFR	Ifrane	1146
6010	MA	MA-JRA	Jerada	1146
6011	MA	MA-KES	Kelaat Sraghna	1146
6012	MA	MA-KEN	Kénitra	1146
6013	MA	MA-KHE	Khemisaet	1146
6014	MA	MA-KHN	Khenifra	1146
6015	MA	MA-KHO	Khouribga	1146
6016	MA	MA-LAA	Laâyoune (EH)	1146
6017	MA	MA-LAP	Larache	1146
6018	MA	MA-MAR	Marrakech	1146
6019	MA	MA-MEK	Meknsès	1146
6020	MA	MA-NAD	Nador	1146
6021	MA	MA-OUA	Ouarzazate	1146
6022	MA	MA-OUD	Oued ed Dahab (EH)	1146
6023	MA	MA-OUJ	Oujda	1146
6024	MA	MA-RBA	Rabat-Salé	1146
6025	MA	MA-SAF	Safi	1146
6026	MA	MA-SEF	Sefrou	1146
6027	MA	MA-SET	Settat	1146
6028	MA	MA-SIK	Sidl Kacem	1146
6029	MA	MA-TNG	Tanger	1146
6030	MA	MA-TNT	Tan-Tan	1146
6031	MA	MA-TAO	Taounate	1146
6032	MA	MA-TAR	Taroudannt	1146
6033	MA	MA-TAT	Tata	1146
6034	MA	MA-TAZ	Taza	1146
6035	MA	MA-TET	Tétouan	1146
6036	MA	MA-TIZ	Tiznit	1146
6037	MD	MD-GA	Gagauzia, Unitate Teritoriala Autonoma	1142
6038	MD	MD-CU	Chisinau	1142
6039	MD	MD-SN	Stinga Nistrului, unitatea teritoriala din	1142
6040	MD	MD-BA	Balti	1142
6041	MD	MD-CA	Cahul	1142
6042	MD	MD-ED	Edinet	1142
6043	MD	MD-LA	Lapusna	1142
6044	MD	MD-OR	Orhei	1142
6045	MD	MD-SO	Soroca	1142
6046	MD	MD-TA	Taraclia	1142
6047	MD	MD-TI	Tighina [Bender]	1142
6048	MD	MD-UN	Ungheni	1142
6049	MG	MG-T	Antananarivo	1129
6050	MG	MG-D	Antsiranana	1129
6051	MG	MG-F	Fianarantsoa	1129
6052	MG	MG-M	Mahajanga	1129
6053	MG	MG-A	Toamasina	1129
6054	MG	MG-U	Toliara	1129
6055	MH	MH-ALL	Ailinglapalap	1135
6056	MH	MH-ALK	Ailuk	1135
6057	MH	MH-ARN	Arno	1135
6058	MH	MH-AUR	Aur	1135
6059	MH	MH-EBO	Ebon	1135
6060	MH	MH-ENI	Eniwetok	1135
6061	MH	MH-JAL	Jaluit	1135
6062	MH	MH-KIL	Kili	1135
6063	MH	MH-KWA	Kwajalein	1135
6064	MH	MH-LAE	Lae	1135
6065	MH	MH-LIB	Lib	1135
6066	MH	MH-LIK	Likiep	1135
6067	MH	MH-MAJ	Majuro	1135
6068	MH	MH-MAL	Maloelap	1135
6069	MH	MH-MEJ	Mejit	1135
6070	MH	MH-MIL	Mili	1135
6071	MH	MH-NMK	Namorik	1135
6072	MH	MH-NMU	Namu	1135
6073	MH	MH-RON	Rongelap	1135
6074	MH	MH-UJA	Ujae	1135
6075	MH	MH-UJL	Ujelang	1135
6076	MH	MH-UTI	Utirik	1135
6077	MH	MH-WTN	Wotho	1135
6078	MH	MH-WTJ	Wotje	1135
6079	ML	ML-BK0	Bamako	1133
6080	ML	ML-7	Gao	1133
6081	ML	ML-1	Kayes	1133
6082	ML	ML-8	Kidal	1133
6083	ML	ML-2	Xoulikoro	1133
6084	ML	ML-5	Mopti	1133
6085	ML	ML-4	S69ou	1133
6086	ML	ML-3	Sikasso	1133
6087	ML	ML-6	Tombouctou	1133
6088	MM	MM-07	Ayeyarwady	1035
6089	MM	MM-02	Bago	1035
6090	MM	MM-03	Magway	1035
6091	MM	MM-04	Mandalay	1035
6092	MM	MM-01	Sagaing	1035
6093	MM	MM-05	Tanintharyi	1035
6094	MM	MM-06	Yangon	1035
6095	MM	MM-14	Chin	1035
6096	MM	MM-11	Kachin	1035
6097	MM	MM-12	Kayah	1035
6098	MM	MM-13	Kayin	1035
6099	MM	MM-15	Mon	1035
6100	MM	MM-16	Rakhine	1035
6101	MM	MM-17	Shan	1035
6102	MN	MN-1	Ulanbaatar	1144
6103	MN	MN-073	Arhangay	1144
6104	MN	MN-069	Bayanhongor	1144
6105	MN	MN-071	Bayan-Olgiy	1144
6106	MN	MN-067	Bulgan	1144
6107	MN	MN-037	Darhan uul	1144
6108	MN	MN-061	Dornod	1144
6109	MN	MN-063	Dornogov,	1144
6110	MN	MN-059	DundgovL	1144
6111	MN	MN-057	Dzavhan	1144
6112	MN	MN-065	Govi-Altay	1144
6113	MN	MN-064	Govi-Smber	1144
6114	MN	MN-039	Hentiy	1144
6115	MN	MN-043	Hovd	1144
6116	MN	MN-041	Hovsgol	1144
6117	MN	MN-053	Omnogovi	1144
6118	MN	MN-035	Orhon	1144
6119	MN	MN-055	Ovorhangay	1144
6120	MN	MN-049	Selenge	1144
6121	MN	MN-051	Shbaatar	1144
6122	MN	MN-047	Tov	1144
6123	MN	MN-046	Uvs	1144
6124	MR	MR-NKC	Nouakchott	1137
6125	MR	MR-03	Assaba	1137
6126	MR	MR-05	Brakna	1137
6127	MR	MR-08	Dakhlet Nouadhibou	1137
6128	MR	MR-04	Gorgol	1137
6129	MR	MR-10	Guidimaka	1137
6130	MR	MR-01	Hodh ech Chargui	1137
6131	MR	MR-02	Hodh el Charbi	1137
6132	MR	MR-12	Inchiri	1137
6133	MR	MR-09	Tagant	1137
6134	MR	MR-11	Tiris Zemmour	1137
6135	MR	MR-06	Trarza	1137
6136	MU	MU-BR	Beau Bassin-Rose Hill	1138
6137	MU	MU-CU	Curepipe	1138
6138	MU	MU-PU	Port Louis	1138
6139	MU	MU-QB	Quatre Bornes	1138
6140	MU	MU-VP	Vacosa-Phoenix	1138
6141	MU	MU-BL	Black River	1138
6142	MU	MU-FL	Flacq	1138
6143	MU	MU-GP	Grand Port	1138
6144	MU	MU-MO	Moka	1138
6145	MU	MU-PA	Pamplemousses	1138
6146	MU	MU-PW	Plaines Wilhems	1138
6147	MU	MU-RP	Riviere du Rempart	1138
6148	MU	MU-SA	Savanne	1138
6149	MU	MU-AG	Agalega Islands	1138
6150	MU	MU-CC	Cargados Carajos Shoals	1138
6151	MU	MU-RO	Rodrigues Island	1138
6152	MV	MV-MLE	Male	1132
6153	MV	MV-02	Alif	1132
6154	MV	MV-20	Baa	1132
6155	MV	MV-17	Dhaalu	1132
6156	MV	MV-14	Faafu	1132
6157	MV	MV-27	Gaaf Alif	1132
6158	MV	MV-28	Gaefu Dhaalu	1132
6159	MV	MV-29	Gnaviyani	1132
6160	MV	MV-07	Haa Alif	1132
6161	MV	MV-23	Haa Dhaalu	1132
6162	MV	MV-26	Kaafu	1132
6163	MV	MV-05	Laamu	1132
6164	MV	MV-03	Lhaviyani	1132
6165	MV	MV-12	Meemu	1132
6166	MV	MV-25	Noonu	1132
6167	MV	MV-13	Raa	1132
6168	MV	MV-01	Seenu	1132
6169	MV	MV-24	Shaviyani	1132
6170	MV	MV-08	Thaa	1132
6171	MV	MV-04	Vaavu	1132
6172	MW	MW-BA	Balaka	1130
6173	MW	MW-BL	Blantyre	1130
6174	MW	MW-CK	Chikwawa	1130
6175	MW	MW-CR	Chiradzulu	1130
6176	MW	MW-CT	Chitipa	1130
6177	MW	MW-DE	Dedza	1130
6178	MW	MW-DO	Dowa	1130
6179	MW	MW-KR	Karonga	1130
6180	MW	MW-KS	Kasungu	1130
6181	MW	MW-LK	Likoma Island	1130
6182	MW	MW-LI	Lilongwe	1130
6183	MW	MW-MH	Machinga	1130
6184	MW	MW-MG	Mangochi	1130
6185	MW	MW-MC	Mchinji	1130
6186	MW	MW-MU	Mulanje	1130
6187	MW	MW-MW	Mwanza	1130
6188	MW	MW-MZ	Mzimba	1130
6189	MW	MW-NB	Nkhata Bay	1130
6190	MW	MW-NK	Nkhotakota	1130
6191	MW	MW-NS	Nsanje	1130
6192	MW	MW-NU	Ntcheu	1130
6193	MW	MW-NI	Ntchisi	1130
6194	MW	MW-PH	Phalomba	1130
6195	MW	MW-RU	Rumphi	1130
6196	MW	MW-SA	Salima	1130
6197	MW	MW-TH	Thyolo	1130
6198	MW	MW-ZO	Zomba	1130
6199	MX	MX-AGU	Aguascalientes	1140
6200	MX	MX-BCN	Baja California	1140
6201	MX	MX-BCS	Baja California Sur	1140
6202	MX	MX-CAM	Campeche	1140
6203	MX	MX-COA	Coahu ila	1140
6204	MX	MX-COL	Col ima	1140
6205	MX	MX-CHP	Chiapas	1140
6206	MX	MX-CHH	Chihushua	1140
6207	MX	MX-DUR	Durango	1140
6208	MX	MX-GUA	Guanajuato	1140
6209	MX	MX-GRO	Guerrero	1140
6210	MX	MX-HID	Hidalgo	1140
6211	MX	MX-JAL	Jalisco	1140
6212	MX	MX-MEX	Mexico	1140
6213	MX	MX-MIC	Michoacin	1140
6214	MX	MX-MOR	Moreloa	1140
6215	MX	MX-NAY	Nayarit	1140
6216	MX	MX-NLE	Nuevo Leon	1140
6217	MX	MX-OAX	Oaxaca	1140
6218	MX	MX-PUE	Puebla	1140
6219	MX	MX-QUE	Queretaro	1140
6220	MX	MX-ROO	Quintana Roo	1140
6221	MX	MX-SLP	San Luis Potosi	1140
6222	MX	MX-SIN	Sinaloa	1140
6223	MX	MX-SON	Sonora	1140
6224	MX	MX-TAB	Tabasco	1140
6225	MX	MX-TAM	Tamaulipas	1140
6226	MX	MX-TLA	Tlaxcala	1140
6227	MX	MX-VER	Veracruz	1140
6228	MX	MX-YUC	Yucatan	1140
6229	MX	MX-ZAC	Zacatecas	1140
6230	MY	MY-14	Wilayah Persekutuan Kuala Lumpur	1131
6231	MY	MY-15	Wilayah Persekutuan Labuan	1131
6232	MY	MY-16	Wilayah Persekutuan Putrajaya	1131
6233	MY	MY-01	Johor	1131
6234	MY	MY-02	Kedah	1131
6235	MY	MY-03	Kelantan	1131
6236	MY	MY-04	Melaka	1131
6237	MY	MY-05	Negeri Sembilan	1131
6238	MY	MY-06	Pahang	1131
6239	MY	MY-08	Perak	1131
6240	MY	MY-09	Perlis	1131
6241	MY	MY-07	Pulau Pinang	1131
6242	MY	MY-12	Sabah	1131
6243	MY	MY-13	Sarawak	1131
6244	MY	MY-10	Selangor	1131
6245	MY	MY-11	Terengganu	1131
6246	MZ	MZ-MPM	Maputo	1147
6247	MZ	MZ-P	Cabo Delgado	1147
6248	MZ	MZ-G	Gaza	1147
6249	MZ	MZ-I	Inhambane	1147
6250	MZ	MZ-B	Manica	1147
6251	MZ	MZ-N	Numpula	1147
6252	MZ	MZ-A	Niaaea	1147
6253	MZ	MZ-S	Sofala	1147
6254	MZ	MZ-T	Tete	1147
6255	MZ	MZ-Q	Zambezia	1147
6256	NA	NA-CA	Caprivi	1148
6257	NA	NA-ER	Erongo	1148
6258	NA	NA-HA	Hardap	1148
6259	NA	NA-KA	Karas	1148
6260	NA	NA-KH	Khomae	1148
6261	NA	NA-KU	Kunene	1148
6262	NA	NA-OW	Ohangwena	1148
6263	NA	NA-OK	Okavango	1148
6264	NA	NA-OH	Omaheke	1148
6265	NA	NA-OS	Omusati	1148
6266	NA	NA-ON	Oshana	1148
6267	NA	NA-OT	Oshikoto	1148
6268	NA	NA-OD	Otjozondjupa	1148
6269	NE	NE-8	Niamey	1156
6270	NE	NE-1	Agadez	1156
6271	NE	NE-2	Diffa	1156
6272	NE	NE-3	Dosso	1156
6273	NE	NE-4	Maradi	1156
6274	NE	NE-S	Tahoua	1156
6275	NE	NE-6	Tillaberi	1156
6276	NE	NE-7	Zinder	1156
6277	NG	NG-FC	Abuja Capital Territory	1157
6278	NG	NG-AB	Abia	1157
6279	NG	NG-AD	Adamawa	1157
6280	NG	NG-AK	Akwa Ibom	1157
6281	NG	NG-AN	Anambra	1157
6282	NG	NG-BA	Bauchi	1157
6283	NG	NG-BY	Bayelsa	1157
6284	NG	NG-BE	Benue	1157
6285	NG	NG-BO	Borno	1157
6286	NG	NG-CR	Cross River	1157
6287	NG	NG-DE	Delta	1157
6288	NG	NG-EB	Ebonyi	1157
6289	NG	NG-ED	Edo	1157
6290	NG	NG-EK	Ekiti	1157
6291	NG	NG-EN	Enugu	1157
6292	NG	NG-GO	Gombe	1157
6293	NG	NG-IM	Imo	1157
6294	NG	NG-JI	Jigawa	1157
6295	NG	NG-KD	Kaduna	1157
6296	NG	NG-KN	Kano	1157
6297	NG	NG-KT	Katsina	1157
6298	NG	NG-KE	Kebbi	1157
6299	NG	NG-KO	Kogi	1157
6300	NG	NG-KW	Kwara	1157
6301	NG	NG-LA	Lagos	1157
6302	NG	NG-NA	Nassarawa	1157
6303	NG	NG-NI	Niger	1157
6304	NG	NG-OG	Ogun	1157
6305	NG	NG-ON	Ondo	1157
6306	NG	NG-OS	Osun	1157
6307	NG	NG-OY	Oyo	1157
6308	NG	NG-RI	Rivers	1157
6309	NG	NG-SO	Sokoto	1157
6310	NG	NG-TA	Taraba	1157
6311	NG	NG-YO	Yobe	1157
6312	NG	NG-ZA	Zamfara	1157
6313	NI	NI-BO	Boaco	1155
6314	NI	NI-CA	Carazo	1155
6315	NI	NI-CI	Chinandega	1155
6316	NI	NI-CO	Chontales	1155
6317	NI	NI-ES	Esteli	1155
6318	NI	NI-JI	Jinotega	1155
6319	NI	NI-LE	Leon	1155
6320	NI	NI-MD	Madriz	1155
6321	NI	NI-MN	Managua	1155
6322	NI	NI-MS	Masaya	1155
6323	NI	NI-MT	Matagalpa	1155
6324	NI	NI-NS	Nueva Segovia	1155
6325	NI	NI-SJ	Rio San Juan	1155
6326	NI	NI-RI	Rivas	1155
6327	NI	NI-AN	Atlantico Norte	1155
6328	NI	NI-AS	Atlantico Sur	1155
6329	NL	NL-DR	Drente	1152
6330	NL	NL-FL	Flevoland	1152
6331	NL	NL-FR	Friesland	1152
6332	NL	NL-GL	Gelderland	1152
6333	NL	NL-GR	Groningen	1152
6334	NL	NL-NB	Noord-Brabant	1152
6335	NL	NL-NH	Noord-Holland	1152
6336	NL	NL-OV	Overijssel	1152
6337	NL	NL-UT	Utrecht	1152
6338	NL	NL-ZH	Zuid-Holland	1152
6339	NL	NL-ZL	Zeeland	1152
6340	NO	NO-02	Akershus	1161
6341	NO	NO-09	Aust-Agder	1161
6342	NO	NO-06	Buskerud	1161
6343	NO	NO-20	Finumark	1161
6344	NO	NO-04	Hedmark	1161
6345	NO	NO-12	Hordaland	1161
6346	NO	NO-15	Mire og Romsdal	1161
6347	NO	NO-18	Nordland	1161
6348	NO	NO-17	Nord-Trindelag	1161
6349	NO	NO-05	Oppland	1161
6350	NO	NO-03	Oslo	1161
6351	NO	NO-11	Rogaland	1161
6352	NO	NO-14	Sogn og Fjordane	1161
6353	NO	NO-16	Sir-Trindelag	1161
6354	NO	NO-06	Telemark	1161
6355	NO	NO-19	Troms	1161
6356	NO	NO-10	Vest-Agder	1161
6357	NO	NO-07	Vestfold	1161
6358	NO	NO-01	Ostfold	1161
6359	NO	NO-22	Jan Mayen	1161
6360	NO	NO-21	Svalbard	1161
6361	NZ	NZ-AUK	Auckland	1154
6362	NZ	NZ-BOP	Bay of Plenty	1154
6363	NZ	NZ-CAN	Canterbury	1154
6364	NZ	NZ-GIS	Gisborne	1154
6365	NZ	NZ-HKB	Hawkes's Bay	1154
6366	NZ	NZ-MWT	Manawatu-Wanganui	1154
6367	NZ	NZ-MBH	Marlborough	1154
6368	NZ	NZ-NSN	Nelson	1154
6369	NZ	NZ-NTL	Northland	1154
6370	NZ	NZ-OTA	Otago	1154
6371	NZ	NZ-STL	Southland	1154
6372	NZ	NZ-TKI	Taranaki	1154
6373	NZ	NZ-TAS	Tasman	1154
6374	NZ	NZ-WKO	waikato	1154
6375	NZ	NZ-WGN	Wellington	1154
6376	NZ	NZ-WTC	West Coast	1154
6377	OM	OM-DA	Ad Dakhillyah	1162
6378	OM	OM-BA	Al Batinah	1162
6379	OM	OM-JA	Al Janblyah	1162
6380	OM	OM-WU	Al Wusta	1162
6381	OM	OM-SH	Ash Sharqlyah	1162
6382	OM	OM-ZA	Az Zahirah	1162
6383	OM	OM-MA	Masqat	1162
6384	OM	OM-MU	Musandam	1162
6385	PA	PA-1	Bocas del Toro	1166
6386	PA	PA-2	Cocle	1166
6387	PA	PA-4	Chiriqui	1166
6388	PA	PA-5	Darien	1166
6389	PA	PA-6	Herrera	1166
6390	PA	PA-7	Loa Santoa	1166
6391	PA	PA-8	Panama	1166
6392	PA	PA-9	Veraguas	1166
6393	PA	PA-Q	Comarca de San Blas	1166
6394	PE	PE-CAL	El Callao	1169
6395	PE	PE-ANC	Ancash	1169
6396	PE	PE-APU	Apurimac	1169
6397	PE	PE-ARE	Arequipa	1169
6398	PE	PE-AYA	Ayacucho	1169
6399	PE	PE-CAJ	Cajamarca	1169
6400	PE	PE-CUS	Cuzco	1169
6401	PE	PE-HUV	Huancavelica	1169
6402	PE	PE-HUC	Huanuco	1169
6403	PE	PE-ICA	Ica	1169
6404	PE	PE-JUN	Junin	1169
6405	PE	PE-LAL	La Libertad	1169
6406	PE	PE-LAM	Lambayeque	1169
6407	PE	PE-LIM	Lima	1169
6408	PE	PE-LOR	Loreto	1169
6409	PE	PE-MDD	Madre de Dios	1169
6410	PE	PE-MOQ	Moquegua	1169
6411	PE	PE-PAS	Pasco	1169
6412	PE	PE-PIU	Piura	1169
6413	PE	PE-PUN	Puno	1169
6414	PE	PE-SAM	San Martin	1169
6415	PE	PE-TAC	Tacna	1169
6416	PE	PE-TUM	Tumbes	1169
6417	PE	PE-UCA	Ucayali	1169
6418	PG	PG-NCD	National Capital District (Port Moresby)	1167
6419	PG	PG-CPK	Chimbu	1167
6420	PG	PG-EHG	Eastern Highlands	1167
6421	PG	PG-EBR	East New Britain	1167
6422	PG	PG-ESW	East Sepik	1167
6423	PG	PG-EPW	Enga	1167
6424	PG	PG-GPK	Gulf	1167
6425	PG	PG-MPM	Madang	1167
6426	PG	PG-MRL	Manus	1167
6427	PG	PG-MBA	Milne Bay	1167
6428	PG	PG-MPL	Morobe	1167
6429	PG	PG-NIK	New Ireland	1167
6430	PG	PG-NSA	North Solomons	1167
6431	PG	PG-SAN	Santaun	1167
6432	PG	PG-SHM	Southern Highlands	1167
6433	PG	PG-WHM	Western Highlands	1167
6434	PG	PG-WBK	West New Britain	1167
6435	PH	PH-ABR	Abra	1170
6436	PH	PH-AGN	Agusan del Norte	1170
6437	PH	PH-AGS	Agusan del Sur	1170
6438	PH	PH-AKL	Aklan	1170
6439	PH	PH-ALB	Albay	1170
6440	PH	PH-ANT	Antique	1170
6441	PH	PH-APA	Apayao	1170
6442	PH	PH-AUR	Aurora	1170
6443	PH	PH-BAS	Basilan	1170
6444	PH	PH-BAN	Batasn	1170
6445	PH	PH-BTN	Batanes	1170
6446	PH	PH-BTG	Batangas	1170
6447	PH	PH-BEN	Benguet	1170
6448	PH	PH-BIL	Biliran	1170
6449	PH	PH-BOH	Bohol	1170
6450	PH	PH-BUK	Bukidnon	1170
6451	PH	PH-BUL	Bulacan	1170
6452	PH	PH-CAG	Cagayan	1170
6453	PH	PH-CAN	Camarines Norte	1170
6454	PH	PH-CAS	Camarines Sur	1170
6455	PH	PH-CAM	Camiguin	1170
6456	PH	PH-CAP	Capiz	1170
6457	PH	PH-CAT	Catanduanes	1170
6458	PH	PH-CAV	Cavite	1170
6459	PH	PH-CEB	Cebu	1170
6460	PH	PH-COM	Compostela Valley	1170
6461	PH	PH-DAV	Davao	1170
6462	PH	PH-DAS	Davao del Sur	1170
6463	PH	PH-DAO	Davao Oriental	1170
6464	PH	PH-EAS	Eastern Samar	1170
6465	PH	PH-GUI	Guimaras	1170
6466	PH	PH-IFU	Ifugao	1170
6467	PH	PH-ILN	Ilocos Norte	1170
6468	PH	PH-ILS	Ilocos Sur	1170
6469	PH	PH-ILI	Iloilo	1170
6470	PH	PH-ISA	Isabela	1170
6471	PH	PH-KAL	Kalinga-Apayso	1170
6472	PH	PH-LAG	Laguna	1170
6473	PH	PH-LAN	Lanao del Norte	1170
6474	PH	PH-LAS	Lanao del Sur	1170
6475	PH	PH-LUN	La Union	1170
6476	PH	PH-LEY	Leyte	1170
6477	PH	PH-MAG	Maguindanao	1170
6478	PH	PH-MAD	Marinduque	1170
6479	PH	PH-MAS	Masbate	1170
6480	PH	PH-MDC	Mindoro Occidental	1170
6481	PH	PH-MDR	Mindoro Oriental	1170
6482	PH	PH-MSC	Misamis Occidental	1170
6483	PH	PH-MSR	Misamis Oriental	1170
6484	PH	PH-MOU	Mountain Province	1170
6485	PH	PH-NEC	Negroe Occidental	1170
6486	PH	PH-NER	Negros Oriental	1170
6487	PH	PH-NCO	North Cotabato	1170
6488	PH	PH-NSA	Northern Samar	1170
6489	PH	PH-NUE	Nueva Ecija	1170
6490	PH	PH-NUV	Nueva Vizcaya	1170
6491	PH	PH-PLW	Palawan	1170
6492	PH	PH-PAM	Pampanga	1170
6493	PH	PH-PAN	Pangasinan	1170
6494	PH	PH-QUE	Quezon	1170
6495	PH	PH-QUI	Quirino	1170
6496	PH	PH-RIZ	Rizal	1170
6497	PH	PH-ROM	Romblon	1170
6498	PH	PH-SAR	Sarangani	1170
6499	PH	PH-SIG	Siquijor	1170
6500	PH	PH-SOR	Sorsogon	1170
6501	PH	PH-SCO	South Cotabato	1170
6502	PH	PH-SLE	Southern Leyte	1170
6503	PH	PH-SUK	Sultan Kudarat	1170
6504	PH	PH-SLU	Sulu	1170
6505	PH	PH-SUN	Surigao del Norte	1170
6506	PH	PH-SUR	Surigao del Sur	1170
6507	PH	PH-TAR	Tarlac	1170
6508	PH	PH-TAW	Tawi-Tawi	1170
6509	PH	PH-WSA	Western Samar	1170
6510	PH	PH-ZMB	Zambales	1170
6511	PH	PH-ZAN	Zamboanga del Norte	1170
6512	PH	PH-ZAS	Zamboanga del Sur	1170
6513	PH	PH-ZSI	Zamboanga Sibiguey	1170
6514	PK	PK-IS	Islamabad	1163
6515	PK	PK-BA	Baluchistan (en)	1163
6516	PK	PK-NW	North-West Frontier	1163
6517	PK	PK-SD	Sind (en)	1163
6518	PK	PK-TA	Federally Administered Tribal Aresa	1163
6519	PK	PK-JK	Azad Rashmir	1163
6520	PK	PK-NA	Northern Areas	1163
6521	PL	PL-DS	Dolnośląskie	1172
6522	PL	PL-KP	Kujawsko-pomorskie	1172
6523	PL	PL-LU	Lubelskie	1172
6524	PL	PL-LB	Lubuskie	1172
6525	PL	PL-LD	Łódzkie	1172
6526	PL	PL-MA	Małopolskie	1172
6527	PL	PL-MZ	Mazowieckie	1172
6528	PL	PL-OP	Opolskie	1172
6529	PL	PL-PK	Podkarpackie	1172
6530	PL	PL-PD	Podlaskie	1172
6531	PL	PL-PM	Pomorskie	1172
6532	PL	PL-SL	Śląskie	1172
6533	PL	PL-SK	Świętokrzyskie	1172
6534	PL	PL-WN	Warmińsko-mazurskie	1172
6535	PL	PL-WP	Wielkopolskie	1172
6536	PL	PL-ZP	Zachodniopomorskie	1172
6537	PT	PT-01	Aveiro	1173
6538	PT	PT-02	Beja	1173
6539	PT	PT-03	Braga	1173
6540	PT	PT-04	Braganca	1173
6541	PT	PT-05	Castelo Branco	1173
6542	PT	PT-06	Colmbra	1173
6543	PT	PT-07	Ovora	1173
6544	PT	PT-08	Faro	1173
6545	PT	PT-09	Guarda	1173
6546	PT	PT-10	Leiria	1173
6547	PT	PT-11	Lisboa	1173
6548	PT	PT-12	Portalegre	1173
6549	PT	PT-13	Porto	1173
6550	PT	PT-14	Santarem	1173
6551	PT	PT-15	Setubal	1173
6552	PT	PT-16	Viana do Castelo	1173
6553	PT	PT-17	Vila Real	1173
6554	PT	PT-18	Viseu	1173
6555	PT	PT-20	Regiao Autonoma dos Acores	1173
6556	PT	PT-30	Regiao Autonoma da Madeira	1173
6557	PY	PY-ASU	Asuncion	1168
6558	PY	PY-16	Alto Paraguay	1168
6559	PY	PY-10	Alto Parana	1168
6560	PY	PY-13	Amambay	1168
6561	PY	PY-19	Boqueron	1168
6562	PY	PY-5	Caeguazu	1168
6563	PY	PY-6	Caazapl	1168
6564	PY	PY-14	Canindeyu	1168
6565	PY	PY-1	Concepcion	1168
6566	PY	PY-3	Cordillera	1168
6567	PY	PY-4	Guaira	1168
6568	PY	PY-7	Itapua	1168
6569	PY	PY-8	Miaiones	1168
6570	PY	PY-12	Neembucu	1168
6571	PY	PY-9	Paraguari	1168
6572	PY	PY-15	Presidente Hayes	1168
6573	PY	PY-2	San Pedro	1168
6574	QA	QA-DA	Ad Dawhah	1175
6575	QA	QA-GH	Al Ghuwayriyah	1175
6576	QA	QA-JU	Al Jumayliyah	1175
6577	QA	QA-KH	Al Khawr	1175
6578	QA	QA-WA	Al Wakrah	1175
6579	QA	QA-RA	Ar Rayyan	1175
6580	QA	QA-JB	Jariyan al Batnah	1175
6581	QA	QA-MS	Madinat ash Shamal	1175
6582	QA	QA-US	Umm Salal	1175
6583	RO	RO-B	Bucuresti	1176
6584	RO	RO-AB	Alba	1176
6585	RO	RO-AR	Arad	1176
6586	RO	RO-AG	Arges	1176
6587	RO	RO-BC	Bacau	1176
6588	RO	RO-BH	Bihor	1176
6589	RO	RO-BN	Bistrita-Nasaud	1176
6590	RO	RO-BT	Boto'ani	1176
6591	RO	RO-BV	Bra'ov	1176
6592	RO	RO-BR	Braila	1176
6593	RO	RO-BZ	Buzau	1176
6594	RO	RO-CS	Caras-Severin	1176
6595	RO	RO-CL	Ca la ras'i	1176
6596	RO	RO-CJ	Cluj	1176
6597	RO	RO-CT	Constant'a	1176
6598	RO	RO-CV	Covasna	1176
6599	RO	RO-DB	Dambovit'a	1176
6600	RO	RO-DJ	Dolj	1176
6601	RO	RO-GL	Galat'i	1176
6602	RO	RO-GR	Giurgiu	1176
6603	RO	RO-GJ	Gorj	1176
6604	RO	RO-HR	Harghita	1176
6605	RO	RO-HD	Hunedoara	1176
6606	RO	RO-IL	Ialomit'a	1176
6607	RO	RO-IS	Ias'i	1176
6608	RO	RO-IF	Ilfov	1176
6609	RO	RO-MM	Maramures	1176
6610	RO	RO-MH	Mehedint'i	1176
6611	RO	RO-MS	Mures	1176
6612	RO	RO-NT	Neamt	1176
6613	RO	RO-OT	Olt	1176
6614	RO	RO-PH	Prahova	1176
6615	RO	RO-SM	Satu Mare	1176
6616	RO	RO-SJ	Sa laj	1176
6617	RO	RO-SB	Sibiu	1176
6618	RO	RO-SV	Suceava	1176
6619	RO	RO-TR	Teleorman	1176
6620	RO	RO-TM	Timis	1176
6621	RO	RO-TL	Tulcea	1176
6622	RO	RO-VS	Vaslui	1176
6623	RO	RO-VL	Valcea	1176
6624	RO	RO-VN	Vrancea	1176
6625	RU	RU-AD	Adygeya, Respublika	1177
6626	RU	RU-AL	Altay, Respublika	1177
6627	RU	RU-BA	Bashkortostan, Respublika	1177
6628	RU	RU-BU	Buryatiya, Respublika	1177
6629	RU	RU-CE	Chechenskaya Respublika	1177
6630	RU	RU-CU	Chuvashskaya Respublika	1177
6631	RU	RU-DA	Dagestan, Respublika	1177
6632	RU	RU-IN	Ingushskaya Respublika	1177
6633	RU	RU-KB	Kabardino-Balkarskaya	1177
6634	RU	RU-KL	Kalmykiya, Respublika	1177
6635	RU	RU-KC	Karachayevo-Cherkesskaya Respublika	1177
6636	RU	RU-KR	Kareliya, Respublika	1177
6637	RU	RU-KK	Khakasiya, Respublika	1177
6638	RU	RU-KO	Komi, Respublika	1177
6639	RU	RU-ME	Mariy El, Respublika	1177
6640	RU	RU-MO	Mordoviya, Respublika	1177
6641	RU	RU-SA	Sakha, Respublika [Yakutiya]	1177
6642	RU	RU-SE	Severnaya Osetiya, Respublika	1177
6643	RU	RU-TA	Tatarstan, Respublika	1177
6644	RU	RU-TY	Tyva, Respublika [Tuva]	1177
6645	RU	RU-UD	Udmurtskaya Respublika	1177
6646	RU	RU-ALT	Altayskiy kray	1177
6647	RU	RU-KHA	Khabarovskiy kray	1177
6648	RU	RU-KDA	Krasnodarskiy kray	1177
6649	RU	RU-KYA	Krasnoyarskiy kray	1177
6650	RU	RU-PRI	Primorskiy kray	1177
6651	RU	RU-STA	Stavropol'skiy kray	1177
6652	RU	RU-AMU	Amurskaya oblast'	1177
6653	RU	RU-ARK	Arkhangel'skaya oblast'	1177
6654	RU	RU-AST	Astrakhanskaya oblast'	1177
6655	RU	RU-BEL	Belgorodskaya oblast'	1177
6656	RU	RU-BRY	Bryanskaya oblast'	1177
6657	RU	RU-CHE	Chelyabinskaya oblast'	1177
6658	RU	RU-CHI	Chitinskaya oblast'	1177
6659	RU	RU-IRK	Irkutskaya oblast'	1177
6660	RU	RU-IVA	Ivanovskaya oblast'	1177
6661	RU	RU-KGD	Kaliningradskaya oblast'	1177
6662	RU	RU-KLU	Kaluzhskaya oblast'	1177
6663	RU	RU-KAM	Kamchatskaya oblast'	1177
6664	RU	RU-KEM	Kemerovskaya oblast'	1177
6665	RU	RU-KIR	Kirovskaya oblast'	1177
6666	RU	RU-KOS	Kostromskaya oblast'	1177
6667	RU	RU-KGN	Kurganskaya oblast'	1177
6668	RU	RU-KRS	Kurskaya oblast'	1177
6669	RU	RU-LEN	Leningradskaya oblast'	1177
6670	RU	RU-LIP	Lipetskaya oblast'	1177
6671	RU	RU-MAG	Magadanskaya oblast'	1177
6672	RU	RU-MOS	Moskovskaya oblast'	1177
6673	RU	RU-MUR	Murmanskaya oblast'	1177
6674	RU	RU-NIZ	Nizhegorodskaya oblast'	1177
6675	RU	RU-NGR	Novgorodskaya oblast'	1177
6676	RU	RU-NVS	Novosibirskaya oblast'	1177
6677	RU	RU-OMS	Omskaya oblast'	1177
6678	RU	RU-ORE	Orenburgskaya oblast'	1177
6679	RU	RU-ORL	Orlovskaya oblast'	1177
6680	RU	RU-PNZ	Penzenskaya oblast'	1177
6681	RU	RU-PER	Permskaya oblast'	1177
6682	RU	RU-PSK	Pskovskaya oblast'	1177
6683	RU	RU-ROS	Rostovskaya oblast'	1177
6684	RU	RU-RYA	Ryazanskaya oblast'	1177
6685	RU	RU-SAK	Sakhalinskaya oblast'	1177
6686	RU	RU-SAM	Samarskaya oblast'	1177
6687	RU	RU-SAR	Saratovskaya oblast'	1177
6688	RU	RU-SMO	Smolenskaya oblast'	1177
6689	RU	RU-SVE	Sverdlovskaya oblast'	1177
6690	RU	RU-TAM	Tambovskaya oblast'	1177
6691	RU	RU-TOM	Tomskaya oblast'	1177
6692	RU	RU-TUL	Tul'skaya oblast'	1177
6693	RU	RU-TVE	Tverskaya oblast'	1177
6694	RU	RU-TYU	Tyumenskaya oblast'	1177
6695	RU	RU-ULY	Ul'yanovskaya oblast'	1177
6696	RU	RU-VLA	Vladimirskaya oblast'	1177
6697	RU	RU-VGG	Volgogradskaya oblast'	1177
6698	RU	RU-VLG	Vologodskaya oblast'	1177
6699	RU	RU-VOR	Voronezhskaya oblast'	1177
6700	RU	RU-YAR	Yaroslavskaya oblast'	1177
6701	RU	RU-MOW	Moskva	1177
6702	RU	RU-SPE	Sankt-Peterburg	1177
6703	RU	RU-YEV	Yevreyskaya avtonomnaya oblast'	1177
6704	RU	RU-AGB	Aginskiy Buryatskiy avtonomnyy	1177
6705	RU	RU-CHU	Chukotskiy avtonomnyy okrug	1177
6706	RU	RU-EVE	Evenkiyskiy avtonomnyy okrug	1177
6707	RU	RU-KHM	Khanty-Mansiyskiy avtonomnyy okrug	1177
6708	RU	RU-KOP	Komi-Permyatskiy avtonomnyy okrug	1177
6709	RU	RU-KOR	Koryakskiy avtonomnyy okrug	1177
6710	RU	RU-NEN	Nenetskiy avtonomnyy okrug	1177
6711	RU	RU-TAY	Taymyrskiy (Dolgano-Nenetskiy)	1177
6712	RU	RU-UOB	Ust'-Ordynskiy Buryatskiy	1177
6713	RU	RU-YAN	Yamalo-Nenetskiy avtonomnyy okrug	1177
6714	RW	RW-C	Butare	1178
6715	RW	RW-I	Byumba	1178
6716	RW	RW-E	Cyangugu	1178
6717	RW	RW-D	Gikongoro	1178
6718	RW	RW-G	Gisenyi	1178
6719	RW	RW-B	Gitarama	1178
6720	RW	RW-J	Kibungo	1178
6721	RW	RW-F	Kibuye	1178
6722	RW	RW-K	Kigali-Rural Kigali y' Icyaro	1178
6723	RW	RW-L	Kigali-Ville Kigali Ngari	1178
6724	RW	RW-M	Mutara	1178
6725	RW	RW-H	Ruhengeri	1178
6726	SA	SA-11	Al Batah	1187
6727	SA	SA-08	Al H,udd ash Shamallyah	1187
6728	SA	SA-12	Al Jawf	1187
6729	SA	SA-03	Al Madinah	1187
6730	SA	SA-05	Al Qasim	1187
6731	SA	SA-01	Ar Riyad	1187
6732	SA	SA-14	Asir	1187
6733	SA	SA-06	Ha'il	1187
6734	SA	SA-09	Jlzan	1187
6735	SA	SA-02	Makkah	1187
6736	SA	SA-10	Najran	1187
6737	SA	SA-07	Tabuk	1187
6738	SB	SB-CT	Capital Territory (Honiara)	1194
6739	SB	SB-GU	Guadalcanal	1194
6740	SB	SB-IS	Isabel	1194
6741	SB	SB-MK	Makira	1194
6742	SB	SB-ML	Malaita	1194
6743	SB	SB-TE	Temotu	1194
6744	SD	SD-23	A'ali an Nil	1200
6745	SD	SD-26	Al Bah al Ahmar	1200
6746	SD	SD-18	Al Buhayrat	1200
6747	SD	SD-07	Al Jazirah	1200
6748	SD	SD-03	Al Khartum	1200
6749	SD	SD-06	Al Qadarif	1200
6750	SD	SD-22	Al Wahdah	1200
6751	SD	SD-04	An Nil	1200
6752	SD	SD-08	An Nil al Abyaq	1200
6753	SD	SD-24	An Nil al Azraq	1200
6754	SD	SD-01	Ash Shamallyah	1200
6755	SD	SD-17	Bahr al Jabal	1200
6756	SD	SD-16	Gharb al Istiwa'iyah	1200
6757	SD	SD-14	Gharb Ba~r al Ghazal	1200
6758	SD	SD-12	Gharb Darfur	1200
6759	SD	SD-10	Gharb Kurdufan	1200
6760	SD	SD-11	Janub Darfur	1200
6761	SD	SD-13	Janub Rurdufan	1200
6762	SD	SD-20	Jnqall	1200
6763	SD	SD-05	Kassala	1200
6764	SD	SD-15	Shamal Batr al Ghazal	1200
6765	SD	SD-02	Shamal Darfur	1200
6766	SD	SD-09	Shamal Kurdufan	1200
6767	SD	SD-19	Sharq al Istiwa'iyah	1200
6768	SD	SD-25	Sinnar	1200
6769	SD	SD-21	Warab	1200
6770	SE	SE-K	Blekinge lan	1204
6771	SE	SE-W	Dalarnas lan	1204
6772	SE	SE-I	Gotlands lan	1204
6773	SE	SE-X	Gavleborge lan	1204
6774	SE	SE-N	Hallands lan	1204
6775	SE	SE-Z	Jamtlande lan	1204
6776	SE	SE-F	Jonkopings lan	1204
6777	SE	SE-H	Kalmar lan	1204
6778	SE	SE-G	Kronoberge lan	1204
6779	SE	SE-BD	Norrbottena lan	1204
6780	SE	SE-M	Skane lan	1204
6781	SE	SE-AB	Stockholms lan	1204
6782	SE	SE-D	Sodermanlands lan	1204
6783	SE	SE-C	Uppsala lan	1204
6784	SE	SE-S	Varmlanda lan	1204
6785	SE	SE-AC	Vasterbottens lan	1204
6786	SE	SE-Y	Vasternorrlands lan	1204
6787	SE	SE-U	Vastmanlanda lan	1204
6788	SE	SE-Q	Vastra Gotalands lan	1204
6789	SE	SE-T	Orebro lan	1204
6790	SE	SE-E	Ostergotlands lan	1204
6791	SH	SH-SH	Saint Helena	1180
6792	SH	SH-AC	Ascension	1180
6793	SH	SH-TA	Tristan da Cunha	1180
6794	SI	SI-001	Ajdovscina	1193
6795	SI	SI-002	Beltinci	1193
6796	SI	SI-148	Benedikt	1193
6797	SI	SI-149	Bistrica ob Sotli	1193
6798	SI	SI-003	Bled	1193
6799	SI	SI-150	Bloke	1193
6800	SI	SI-004	Bohinj	1193
6801	SI	SI-005	Borovnica	1193
6802	SI	SI-006	Bovec	1193
6803	SI	SI-151	Braslovce	1193
6804	SI	SI-007	Brda	1193
6805	SI	SI-008	Brezovica	1193
6806	SI	SI-009	Brezica	1193
6807	SI	SI-152	Cankova	1193
6808	SI	SI-011	Celje	1193
6809	SI	SI-012	Cerklje na Gorenjskem	1193
6810	SI	SI-013	Cerknica	1193
6811	SI	SI-014	Cerkno	1193
6812	SI	SI-153	Cerkvenjak	1193
6813	SI	SI-015	Crensovci	1193
6814	SI	SI-016	Crna na Koroskem	1193
6815	SI	SI-017	Crnomelj	1193
6816	SI	SI-018	Destrnik	1193
6817	SI	SI-019	Divaca	1193
6818	SI	SI-154	Dobje	1193
6819	SI	SI-020	Dobrepolje	1193
6820	SI	SI-155	Dobrna	1193
6821	SI	SI-021	Dobrova-Polhov Gradec	1193
6822	SI	SI-156	Dobrovnik	1193
6823	SI	SI-022	Dol pri Ljubljani	1193
6824	SI	SI-157	Dolenjske Toplice	1193
6825	SI	SI-023	Domzale	1193
6826	SI	SI-024	Dornava	1193
6827	SI	SI-025	Dravograd	1193
6828	SI	SI-026	Duplek	1193
6829	SI	SI-027	Gorenja vas-Poljane	1193
6830	SI	SI-028	Gorsnica	1193
6831	SI	SI-029	Gornja Radgona	1193
6832	SI	SI-030	Gornji Grad	1193
6833	SI	SI-031	Gornji Petrovci	1193
6834	SI	SI-158	Grad	1193
6835	SI	SI-032	Grosuplje	1193
6836	SI	SI-159	Hajdina	1193
6837	SI	SI-160	Hoce-Slivnica	1193
6838	SI	SI-161	Hodos	1193
6839	SI	SI-162	Jorjul	1193
6840	SI	SI-034	Hrastnik	1193
6841	SI	SI-035	Hrpelje-Kozina	1193
6842	SI	SI-036	Idrija	1193
6843	SI	SI-037	Ig	1193
6844	SI	SI-038	IIrska Bistrica	1193
6845	SI	SI-039	Ivancna Gorica	1193
6846	SI	SI-040	Izola	1193
6847	SI	SI-041	Jesenice	1193
6848	SI	SI-163	Jezersko	1193
6849	SI	SI-042	Jursinci	1193
6850	SI	SI-043	Kamnik	1193
6851	SI	SI-044	Kanal	1193
6852	SI	SI-045	Kidricevo	1193
6853	SI	SI-046	Kobarid	1193
6854	SI	SI-047	Kobilje	1193
6855	SI	SI-048	Jovevje	1193
6856	SI	SI-049	Komen	1193
6857	SI	SI-164	Komenda	1193
6858	SI	SI-050	Koper	1193
6859	SI	SI-165	Kostel	1193
6860	SI	SI-051	Kozje	1193
6861	SI	SI-052	Kranj	1193
6862	SI	SI-053	Kranjska Gora	1193
6863	SI	SI-166	Krizevci	1193
6864	SI	SI-054	Krsko	1193
6865	SI	SI-055	Kungota	1193
6866	SI	SI-056	Kuzma	1193
6867	SI	SI-057	Lasko	1193
6868	SI	SI-058	Lenart	1193
6869	SI	SI-059	Lendava	1193
6870	SI	SI-060	Litija	1193
6871	SI	SI-061	Ljubljana	1193
6872	SI	SI-062	Ljubno	1193
6873	SI	SI-063	Ljutomer	1193
6874	SI	SI-064	Logatec	1193
6875	SI	SI-065	Loska dolina	1193
6876	SI	SI-066	Loski Potok	1193
6877	SI	SI-167	Lovrenc na Pohorju	1193
6878	SI	SI-067	Luce	1193
6879	SI	SI-068	Lukovica	1193
6880	SI	SI-069	Majsperk	1193
6881	SI	SI-070	Maribor	1193
6882	SI	SI-168	Markovci	1193
6883	SI	SI-071	Medvode	1193
6884	SI	SI-072	Menges	1193
6885	SI	SI-073	Metlika	1193
6886	SI	SI-074	Mezica	1193
6887	SI	SI-169	Miklavz na Dravskern polju	1193
6888	SI	SI-075	Miren-Kostanjevica	1193
6889	SI	SI-170	Mirna Pec	1193
6890	SI	SI-076	Mislinja	1193
6891	SI	SI-077	Moravce	1193
6892	SI	SI-078	Moravske Toplice	1193
6893	SI	SI-079	Mozirje	1193
6894	SI	SI-080	Murska Sobota	1193
6895	SI	SI-081	Muta	1193
6896	SI	SI-082	Naklo	1193
6897	SI	SI-083	Nazarje	1193
6898	SI	SI-084	Nova Gorica	1193
6899	SI	SI-085	Nova mesto	1193
6900	SI	SI-181	Sveta Ana	1193
6901	SI	SI-182	Sveti Andraz v Slovenskih goricah	1193
6902	SI	SI-116	Sveti Jurij	1193
6903	SI	SI-033	Salovci	1193
6904	SI	SI-183	Sempeter-Vrtojba	1193
6905	SI	SI-117	Sencur	1193
6906	SI	SI-118	Sentilj	1193
6907	SI	SI-119	Sentjernej	1193
6908	SI	SI-120	Sentjur pri Celju	1193
6909	SI	SI-121	Skocjan	1193
6910	SI	SI-122	Skofja Loka	1193
6911	SI	SI-123	Skoftjica	1193
6912	SI	SI-124	Smarje pri Jelsah	1193
6913	SI	SI-125	Smartno ob Paki	1193
6914	SI	SI-194	Smartno pri Litiji	1193
6915	SI	SI-126	Sostanj	1193
6916	SI	SI-127	Store	1193
6917	SI	SI-184	Tabor	1193
6918	SI	SI-010	Tisina	1193
6919	SI	SI-128	Tolmin	1193
6920	SI	SI-129	Trbovje	1193
6921	SI	SI-130	Trebnje	1193
6922	SI	SI-185	Trnovska vas	1193
6923	SI	SI-131	Trzic	1193
6924	SI	SI-186	Trzin	1193
6925	SI	SI-132	Turnisce	1193
6926	SI	SI-133	Velenje	1193
6927	SI	SI-187	Velika Polana	1193
6928	SI	SI-134	Velika Lasce	1193
6929	SI	SI-188	Verzej	1193
6930	SI	SI-135	Videm	1193
6931	SI	SI-136	Vipava	1193
6932	SI	SI-137	Vitanje	1193
6933	SI	SI-138	Vojnik	1193
6934	SI	SI-189	Vransko	1193
6935	SI	SI-140	Vrhnika	1193
6936	SI	SI-141	Vuzenica	1193
6937	SI	SI-142	Zagorje ob Savi	1193
6938	SI	SI-143	Zavrc	1193
6939	SI	SI-144	Zrece	1193
6940	SI	SI-190	Zalec	1193
6941	SI	SI-146	Zelezniki	1193
6942	SI	SI-191	Zetale	1193
6943	SI	SI-147	Ziri	1193
6944	SI	SI-192	Zirovnica	1193
6945	SI	SI-193	Zuzemberk	1193
6946	SK	SK-BC	Banskobystrický kraj	1192
6947	SK	SK-BL	Bratislavský kraj	1192
6948	SK	SK-KI	Košický kraj	1192
6949	SK	SK-NJ	Nitriansky kraj	1192
6950	SK	SK-PV	Prešovský kraj	1192
6951	SK	SK-TC	Trenčiansky kraj	1192
6952	SK	SK-TA	Trnavský kraj	1192
6953	SK	SK-ZI	Žilinský kraj	1192
6954	SL	SL-W	Western Area (Freetown)	1190
6955	SN	SN-DK	Dakar	1188
6956	SN	SN-DB	Diourbel	1188
6957	SN	SN-FK	Fatick	1188
6958	SN	SN-KL	Kaolack	1188
6959	SN	SN-KD	Kolda	1188
6960	SN	SN-LG	Louga	1188
6961	SN	SN-MT	Matam	1188
6962	SN	SN-SL	Saint-Louis	1188
6963	SN	SN-TC	Tambacounda	1188
6964	SN	SN-TH	Thies	1188
6965	SN	SN-ZG	Ziguinchor	1188
6966	SO	SO-AW	Awdal	1195
6967	SO	SO-BK	Bakool	1195
6968	SO	SO-BN	Banaadir	1195
6969	SO	SO-BY	Bay	1195
6970	SO	SO-GA	Galguduud	1195
6971	SO	SO-GE	Gedo	1195
6972	SO	SO-HI	Hiirsan	1195
6973	SO	SO-JD	Jubbada Dhexe	1195
6974	SO	SO-JH	Jubbada Hoose	1195
6975	SO	SO-MU	Mudug	1195
6976	SO	SO-NU	Nugaal	1195
6977	SO	SO-SA	Saneag	1195
6978	SO	SO-SD	Shabeellaha Dhexe	1195
6979	SO	SO-SH	Shabeellaha Hoose	1195
6980	SO	SO-SO	Sool	1195
6981	SO	SO-TO	Togdheer	1195
6982	SO	SO-WO	Woqooyi Galbeed	1195
6983	SR	SR-BR	Brokopondo	1201
6984	SR	SR-CM	Commewijne	1201
6985	SR	SR-CR	Coronie	1201
6986	SR	SR-MA	Marowijne	1201
6987	SR	SR-NI	Nickerie	1201
6988	SR	SR-PM	Paramaribo	1201
6989	SR	SR-SA	Saramacca	1201
6990	SR	SR-SI	Sipaliwini	1201
6991	SR	SR-WA	Wanica	1201
6992	ST	ST-P	Principe	1207
6993	ST	ST-S	Sao Tome	1207
6994	SV	SV-AH	Ahuachapan	1066
6995	SV	SV-CA	Cabanas	1066
6996	SV	SV-CU	Cuscatlan	1066
6997	SV	SV-CH	Chalatenango	1066
6998	SV	SV-MO	Morazan	1066
6999	SV	SV-SM	San Miguel	1066
7000	SV	SV-SS	San Salvador	1066
7001	SV	SV-SA	Santa Ana	1066
7002	SV	SV-SV	San Vicente	1066
7003	SV	SV-SO	Sonsonate	1066
7004	SV	SV-US	Usulutan	1066
7005	SY	SY-HA	Al Hasakah	1206
7006	SY	SY-LA	Al Ladhiqiyah	1206
7007	SY	SY-QU	Al Qunaytirah	1206
7008	SY	SY-RA	Ar Raqqah	1206
7009	SY	SY-SU	As Suwayda'	1206
7010	SY	SY-DR	Dar'a	1206
7011	SY	SY-DY	Dayr az Zawr	1206
7012	SY	SY-DI	Dimashq	1206
7013	SY	SY-HL	Halab	1206
7014	SY	SY-HM	Hamah	1206
7015	SY	SY-HI	Jim'	1206
7016	SY	SY-ID	Idlib	1206
7017	SY	SY-RD	Rif Dimashq	1206
7018	SY	SY-TA	Tarts	1206
7019	SZ	SZ-HH	Hhohho	1203
7020	SZ	SZ-LU	Lubombo	1203
7021	SZ	SZ-MA	Manzini	1203
7022	SZ	SZ-SH	Shiselweni	1203
7023	TD	TD-BA	Batha	1043
7024	TD	TD-BI	Biltine	1043
7025	TD	TD-BET	Borkou-Ennedi-Tibesti	1043
7026	TD	TD-CB	Chari-Baguirmi	1043
7027	TD	TD-GR	Guera	1043
7028	TD	TD-KA	Kanem	1043
7029	TD	TD-LC	Lac	1043
7030	TD	TD-LO	Logone-Occidental	1043
7031	TD	TD-LR	Logone-Oriental	1043
7032	TD	TD-MK	Mayo-Kebbi	1043
7033	TD	TD-MC	Moyen-Chari	1043
7034	TD	TD-OD	Ouaddai	1043
7035	TD	TD-SA	Salamat	1043
7036	TD	TD-TA	Tandjile	1043
7037	TG	TG-K	Kara	1214
7038	TG	TG-M	Maritime (Region)	1214
7039	TG	TG-S	Savannes	1214
7040	TH	TH-10	Krung Thep Maha Nakhon Bangkok	1211
7041	TH	TH-S	Phatthaya	1211
7042	TH	TH-37	Amnat Charoen	1211
7043	TH	TH-15	Ang Thong	1211
7044	TH	TH-31	Buri Ram	1211
7045	TH	TH-24	Chachoengsao	1211
7046	TH	TH-18	Chai Nat	1211
7047	TH	TH-36	Chaiyaphum	1211
7048	TH	TH-22	Chanthaburi	1211
7049	TH	TH-50	Chiang Mai	1211
7050	TH	TH-57	Chiang Rai	1211
7051	TH	TH-20	Chon Buri	1211
7052	TH	TH-86	Chumphon	1211
7053	TH	TH-46	Kalasin	1211
7054	TH	TH-62	Kamphasng Phet	1211
7055	TH	TH-71	Kanchanaburi	1211
7056	TH	TH-40	Khon Kaen	1211
7057	TH	TH-81	Krabi	1211
7058	TH	TH-52	Lampang	1211
7059	TH	TH-51	Lamphun	1211
7060	TH	TH-42	Loei	1211
7061	TH	TH-16	Lop Buri	1211
7062	TH	TH-58	Mae Hong Son	1211
7063	TH	TH-44	Maha Sarakham	1211
7064	TH	TH-49	Mukdahan	1211
7065	TH	TH-26	Nakhon Nayok	1211
7066	TH	TH-73	Nakhon Pathom	1211
7067	TH	TH-48	Nakhon Phanom	1211
7068	TH	TH-30	Nakhon Ratchasima	1211
7069	TH	TH-60	Nakhon Sawan	1211
7070	TH	TH-80	Nakhon Si Thammarat	1211
7071	TH	TH-55	Nan	1211
7072	TH	TH-96	Narathiwat	1211
7073	TH	TH-39	Nong Bua Lam Phu	1211
7074	TH	TH-43	Nong Khai	1211
7075	TH	TH-12	Nonthaburi	1211
7076	TH	TH-13	Pathum Thani	1211
7077	TH	TH-94	Pattani	1211
7078	TH	TH-82	Phangnga	1211
7079	TH	TH-93	Phatthalung	1211
7080	TH	TH-56	Phayao	1211
7081	TH	TH-67	Phetchabun	1211
7082	TH	TH-76	Phetchaburi	1211
7083	TH	TH-66	Phichit	1211
7084	TH	TH-65	Phitsanulok	1211
7085	TH	TH-54	Phrae	1211
7086	TH	TH-14	Phra Nakhon Si Ayutthaya	1211
7087	TH	TH-83	Phaket	1211
7088	TH	TH-25	Prachin Buri	1211
7089	TH	TH-77	Prachuap Khiri Khan	1211
7090	TH	TH-85	Ranong	1211
7091	TH	TH-70	Ratchaburi	1211
7092	TH	TH-21	Rayong	1211
7093	TH	TH-45	Roi Et	1211
7094	TH	TH-27	Sa Kaeo	1211
7095	TH	TH-47	Sakon Nakhon	1211
7096	TH	TH-11	Samut Prakan	1211
7097	TH	TH-74	Samut Sakhon	1211
7098	TH	TH-75	Samut Songkhram	1211
7099	TH	TH-19	Saraburi	1211
7100	TH	TH-91	Satun	1211
7101	TH	TH-17	Sing Buri	1211
7102	TH	TH-33	Si Sa Ket	1211
7103	TH	TH-90	Songkhla	1211
7104	TH	TH-64	Sukhothai	1211
7105	TH	TH-72	Suphan Buri	1211
7106	TH	TH-84	Surat Thani	1211
7107	TH	TH-32	Surin	1211
7108	TH	TH-63	Tak	1211
7109	TH	TH-92	Trang	1211
7110	TH	TH-23	Trat	1211
7111	TH	TH-34	Ubon Ratchathani	1211
7112	TH	TH-41	Udon Thani	1211
7113	TH	TH-61	Uthai Thani	1211
7114	TH	TH-53	Uttaradit	1211
7115	TH	TH-95	Yala	1211
7116	TH	TH-35	Yasothon	1211
7117	TJ	TJ-SU	Sughd	1209
7118	TJ	TJ-KT	Khatlon	1209
7119	TJ	TJ-GB	Gorno-Badakhshan	1209
7120	TM	TM-A	Ahal	1220
7121	TM	TM-B	Balkan	1220
7122	TM	TM-D	Dasoguz	1220
7123	TM	TM-L	Lebap	1220
7124	TM	TM-M	Mary	1220
7125	TN	TN-31	Béja	1218
7126	TN	TN-13	Ben Arous	1218
7127	TN	TN-23	Bizerte	1218
7128	TN	TN-81	Gabès	1218
7129	TN	TN-71	Gafsa	1218
7130	TN	TN-32	Jendouba	1218
7131	TN	TN-41	Kairouan	1218
7132	TN	TN-42	Rasserine	1218
7133	TN	TN-73	Kebili	1218
7134	TN	TN-12	L'Ariana	1218
7135	TN	TN-33	Le Ref	1218
7136	TN	TN-53	Mahdia	1218
7137	TN	TN-14	La Manouba	1218
7138	TN	TN-82	Medenine	1218
7139	TN	TN-52	Moneatir	1218
7140	TN	TN-21	Naboul	1218
7141	TN	TN-61	Sfax	1218
7142	TN	TN-43	Sidi Bouxid	1218
7143	TN	TN-34	Siliana	1218
7144	TN	TN-51	Sousse	1218
7145	TN	TN-83	Tataouine	1218
7146	TN	TN-72	Tozeur	1218
7147	TN	TN-11	Tunis	1218
7148	TN	TN-22	Zaghouan	1218
7149	TR	TR-01	Adana	1219
7150	TR	TR-02	Ad yaman	1219
7151	TR	TR-03	Afyon	1219
7152	TR	TR-04	Ag r	1219
7153	TR	TR-68	Aksaray	1219
7154	TR	TR-05	Amasya	1219
7155	TR	TR-06	Ankara	1219
7156	TR	TR-07	Antalya	1219
7157	TR	TR-75	Ardahan	1219
7158	TR	TR-08	Artvin	1219
7159	TR	TR-09	Aydin	1219
7160	TR	TR-10	Bal kesir	1219
7161	TR	TR-74	Bartin	1219
7162	TR	TR-72	Batman	1219
7163	TR	TR-69	Bayburt	1219
7164	TR	TR-11	Bilecik	1219
7165	TR	TR-12	Bingol	1219
7166	TR	TR-13	Bitlis	1219
7167	TR	TR-14	Bolu	1219
7168	TR	TR-15	Burdur	1219
7169	TR	TR-16	Bursa	1219
7170	TR	TR-17	Canakkale	1219
7171	TR	TR-18	Cankir	1219
7172	TR	TR-19	Corum	1219
7173	TR	TR-20	Denizli	1219
7174	TR	TR-21	Diyarbakir	1219
7175	TR	TR-81	Duzce	1219
7176	TR	TR-22	Edirne	1219
7177	TR	TR-23	Elazig	1219
7178	TR	TR-24	Erzincan	1219
7179	TR	TR-25	Erzurum	1219
7180	TR	TR-26	Eskis'ehir	1219
7181	TR	TR-27	Gaziantep	1219
7182	TR	TR-28	Giresun	1219
7183	TR	TR-29	Gms'hane	1219
7184	TR	TR-30	Hakkari	1219
7185	TR	TR-31	Hatay	1219
7186	TR	TR-76	Igidir	1219
7187	TR	TR-32	Isparta	1219
7188	TR	TR-33	Icel	1219
7189	TR	TR-34	Istanbul	1219
7190	TR	TR-35	Izmir	1219
7191	TR	TR-46	Kahramanmaras	1219
7192	TR	TR-78	Karabk	1219
7193	TR	TR-70	Karaman	1219
7194	TR	TR-36	Kars	1219
7195	TR	TR-37	Kastamonu	1219
7196	TR	TR-38	Kayseri	1219
7197	TR	TR-71	Kirikkale	1219
7198	TR	TR-39	Kirklareli	1219
7199	TR	TR-40	Kirs'ehir	1219
7200	TR	TR-79	Kilis	1219
7201	TR	TR-41	Kocaeli	1219
7202	TR	TR-42	Konya	1219
7203	TR	TR-43	Ktahya	1219
7204	TR	TR-44	Malatya	1219
7205	TR	TR-45	Manisa	1219
7206	TR	TR-47	Mardin	1219
7207	TR	TR-48	Mugila	1219
7208	TR	TR-49	Mus	1219
7209	TR	TR-50	Nevs'ehir	1219
7210	TR	TR-51	Nigide	1219
7211	TR	TR-52	Ordu	1219
7212	TR	TR-80	Osmaniye	1219
7213	TR	TR-53	Rize	1219
7214	TR	TR-54	Sakarya	1219
7215	TR	TR-55	Samsun	1219
7216	TR	TR-56	Siirt	1219
7217	TR	TR-57	Sinop	1219
7218	TR	TR-58	Sivas	1219
7219	TR	TR-63	S'anliurfa	1219
7220	TR	TR-73	S'rnak	1219
7221	TR	TR-59	Tekirdag	1219
7222	TR	TR-60	Tokat	1219
7223	TR	TR-61	Trabzon	1219
7224	TR	TR-62	Tunceli	1219
7225	TR	TR-64	Us'ak	1219
7226	TR	TR-65	Van	1219
7227	TR	TR-77	Yalova	1219
7228	TR	TR-66	Yozgat	1219
7229	TR	TR-67	Zonguldak	1219
7230	TT	TT-CTT	Couva-Tabaquite-Talparo	1217
7231	TT	TT-DMN	Diego Martin	1217
7232	TT	TT-ETO	Eastern Tobago	1217
7233	TT	TT-PED	Penal-Debe	1217
7234	TT	TT-PRT	Princes Town	1217
7235	TT	TT-RCM	Rio Claro-Mayaro	1217
7236	TT	TT-SGE	Sangre Grande	1217
7237	TT	TT-SJL	San Juan-Laventille	1217
7238	TT	TT-SIP	Siparia	1217
7239	TT	TT-TUP	Tunapuna-Piarco	1217
7240	TT	TT-WTO	Western Tobago	1217
7241	TT	TT-ARI	Arima	1217
7242	TT	TT-CHA	Chaguanas	1217
7243	TT	TT-PTF	Point Fortin	1217
7244	TT	TT-POS	Port of Spain	1217
7245	TT	TT-SFO	San Fernando	1217
7246	TL	TL-AL	Aileu	1063
7247	TL	TL-AN	Ainaro	1063
7248	TL	TL-BA	Bacucau	1063
7249	TL	TL-BO	Bobonaro	1063
7250	TL	TL-CO	Cova Lima	1063
7251	TL	TL-DI	Dili	1063
7252	TL	TL-ER	Ermera	1063
7253	TL	TL-LA	Laulem	1063
7254	TL	TL-LI	Liquica	1063
7255	TL	TL-MT	Manatuto	1063
7256	TL	TL-MF	Manafahi	1063
7257	TL	TL-OE	Oecussi	1063
7258	TL	TL-VI	Viqueque	1063
7259	TW	TW-CHA	Changhua	1208
7260	TW	TW-CYQ	Chiayi	1208
7261	TW	TW-HSQ	Hsinchu	1208
7262	TW	TW-HUA	Hualien	1208
7263	TW	TW-ILA	Ilan	1208
7264	TW	TW-KHQ	Kaohsiung	1208
7265	TW	TW-MIA	Miaoli	1208
7266	TW	TW-NAN	Nantou	1208
7267	TW	TW-PEN	Penghu	1208
7268	TW	TW-PIF	Pingtung	1208
7269	TW	TW-TXQ	Taichung	1208
7270	TW	TW-TNQ	Tainan	1208
7271	TW	TW-TPQ	Taipei	1208
7272	TW	TW-TTT	Taitung	1208
7273	TW	TW-TAO	Taoyuan	1208
7274	TW	TW-YUN	Yunlin	1208
7275	TW	TW-KEE	Keelung	1208
7276	TZ	TZ-01	Arusha	1210
7277	TZ	TZ-02	Dar-es-Salaam	1210
7278	TZ	TZ-03	Dodoma	1210
7279	TZ	TZ-04	Iringa	1210
7280	TZ	TZ-05	Kagera	1210
7281	TZ	TZ-06	Kaskazini Pemba	1210
7282	TZ	TZ-07	Kaskazini Unguja	1210
7283	TZ	TZ-08	Xigoma	1210
7284	TZ	TZ-09	Kilimanjaro	1210
7285	TZ	TZ-10	Rusini Pemba	1210
7286	TZ	TZ-11	Kusini Unguja	1210
7287	TZ	TZ-12	Lindi	1210
7288	TZ	TZ-26	Manyara	1210
7289	TZ	TZ-13	Mara	1210
7290	TZ	TZ-14	Mbeya	1210
7291	TZ	TZ-15	Mjini Magharibi	1210
7292	TZ	TZ-16	Morogoro	1210
7293	TZ	TZ-17	Mtwara	1210
7294	TZ	TZ-19	Pwani	1210
7295	TZ	TZ-20	Rukwa	1210
7296	TZ	TZ-21	Ruvuma	1210
7297	TZ	TZ-22	Shinyanga	1210
7298	TZ	TZ-23	Singida	1210
7299	TZ	TZ-24	Tabora	1210
7300	TZ	TZ-25	Tanga	1210
7301	UA	UA-71	Cherkas'ka Oblast'	1224
7302	UA	UA-74	Chernihivs'ka Oblast'	1224
7303	UA	UA-77	Chernivets'ka Oblast'	1224
7304	UA	UA-12	Dnipropetrovs'ka Oblast'	1224
7305	UA	UA-14	Donets'ka Oblast'	1224
7306	UA	UA-26	Ivano-Frankivs'ka Oblast'	1224
7307	UA	UA-63	Kharkivs'ka Oblast'	1224
7308	UA	UA-65	Khersons'ka Oblast'	1224
7309	UA	UA-68	Khmel'nyts'ka Oblast'	1224
7310	UA	UA-35	Kirovohrads'ka Oblast'	1224
7311	UA	UA-32	Kyivs'ka Oblast'	1224
7312	UA	UA-09	Luhans'ka Oblast'	1224
7313	UA	UA-46	L'vivs'ka Oblast'	1224
7314	UA	UA-48	Mykolaivs'ka Oblast'	1224
7315	UA	UA-51	Odes 'ka Oblast'	1224
7316	UA	UA-53	Poltavs'ka Oblast'	1224
7317	UA	UA-56	Rivnens'ka Oblast'	1224
7318	UA	UA-59	Sums 'ka Oblast'	1224
7319	UA	UA-61	Ternopil's'ka Oblast'	1224
7320	UA	UA-05	Vinnyts'ka Oblast'	1224
7321	UA	UA-07	Volyos'ka Oblast'	1224
7322	UA	UA-21	Zakarpats'ka Oblast'	1224
7323	UA	UA-23	Zaporiz'ka Oblast'	1224
7324	UA	UA-18	Zhytomyrs'ka Oblast'	1224
7325	UA	UA-43	Respublika Krym	1224
7326	UA	UA-30	Kyiv	1224
7327	UA	UA-40	Sevastopol	1224
7328	UG	UG-301	Adjumani	1223
7329	UG	UG-302	Apac	1223
7330	UG	UG-303	Arua	1223
7331	UG	UG-201	Bugiri	1223
7332	UG	UG-401	Bundibugyo	1223
7333	UG	UG-402	Bushenyi	1223
7334	UG	UG-202	Busia	1223
7335	UG	UG-304	Gulu	1223
7336	UG	UG-403	Hoima	1223
7337	UG	UG-203	Iganga	1223
7338	UG	UG-204	Jinja	1223
7339	UG	UG-404	Kabale	1223
7340	UG	UG-405	Kabarole	1223
7341	UG	UG-213	Kaberamaido	1223
7342	UG	UG-101	Kalangala	1223
7343	UG	UG-102	Kampala	1223
7344	UG	UG-205	Kamuli	1223
7345	UG	UG-413	Kamwenge	1223
7346	UG	UG-414	Kanungu	1223
7347	UG	UG-206	Kapchorwa	1223
7348	UG	UG-406	Kasese	1223
7349	UG	UG-207	Katakwi	1223
7350	UG	UG-112	Kayunga	1223
7351	UG	UG-407	Kibaale	1223
7352	UG	UG-103	Kiboga	1223
7353	UG	UG-408	Kisoro	1223
7354	UG	UG-305	Kitgum	1223
7355	UG	UG-306	Kotido	1223
7356	UG	UG-208	Kumi	1223
7357	UG	UG-415	Kyenjojo	1223
7358	UG	UG-307	Lira	1223
7359	UG	UG-104	Luwero	1223
7360	UG	UG-105	Masaka	1223
7361	UG	UG-409	Masindi	1223
7362	UG	UG-214	Mayuge	1223
7363	UG	UG-209	Mbale	1223
7364	UG	UG-410	Mbarara	1223
7365	UG	UG-308	Moroto	1223
7366	UG	UG-309	Moyo	1223
7367	UG	UG-106	Mpigi	1223
7368	UG	UG-107	Mubende	1223
7369	UG	UG-108	Mukono	1223
7370	UG	UG-311	Nakapiripirit	1223
7371	UG	UG-109	Nakasongola	1223
7372	UG	UG-310	Nebbi	1223
7373	UG	UG-411	Ntungamo	1223
7374	UG	UG-312	Pader	1223
7375	UG	UG-210	Pallisa	1223
7376	UG	UG-110	Rakai	1223
7377	UG	UG-412	Rukungiri	1223
7378	UG	UG-111	Sembabule	1223
7379	UG	UG-215	Sironko	1223
7380	UG	UG-211	Soroti	1223
7381	UG	UG-212	Tororo	1223
7382	UG	UG-113	Wakiso	1223
7383	UG	UG-313	Yumbe	1223
7384	UM	UM-81	Baker Island	1227
7385	UM	UM-84	Howland Island	1227
7386	UM	UM-86	Jarvis Island	1227
7387	UM	UM-67	Johnston Atoll	1227
7388	UM	UM-89	Kingman Reef	1227
7389	UM	UM-71	Midway Islands	1227
7390	UM	UM-76	Navassa Island	1227
7391	UM	UM-95	Palmyra Atoll	1227
7392	UM	UM-79	Wake Ialand	1227
7393	US	US-AL	Alabama	1228
7394	US	US-AK	Alaska	1228
7395	US	US-AS	American Samoa	1228
7396	US	US-AZ	Arizona	1228
7397	US	US-AR	Arkansas	1228
7398	US	US-CA	California	1228
7399	US	US-CO	Colorado	1228
7400	US	US-CT	Connecticut	1228
7401	US	US-DE	Delaware	1228
7402	US	US-DC	District of Columbia	1228
7403	US	US-FL	Florida	1228
7404	US	US-GA	Georgia	1228
7405	US	US-GU	Guam	1228
7406	US	US-HI	Hawaii	1228
7407	US	US-ID	Idaho	1228
7408	US	US-IL	Illinois	1228
7409	US	US-IN	Indiana	1228
7410	US	US-IA	Iowa	1228
7411	US	US-KS	Kansas	1228
7412	US	US-KY	Kentucky	1228
7413	US	US-LA	Louisiana	1228
7414	US	US-ME	Maine	1228
7415	US	US-MA	Massachusetts	1228
7416	US	US-MI	Michigan	1228
7417	US	US-MN	Minnesota	1228
7418	US	US-MS	Mississippi	1228
7419	US	US-MO	Missouri	1228
7420	US	US-NE	Nebraska	1228
7421	US	US-NV	Nevada	1228
7422	US	US-NH	New Hampshire	1228
7423	US	US-NJ	New Jersey	1228
7424	US	US-NM	New Mexico	1228
7425	US	US-NY	New York	1228
7426	US	US-NC	North Carolina	1228
7427	US	US-ND	North Dakota	1228
7428	US	US-MP	Northern Mariana Islands	1228
7429	US	US-OH	Ohio	1228
7430	US	US-OK	Oklahoma	1228
7431	US	US-OR	Oregon	1228
7432	US	US-PA	Pennsylvania	1228
7433	US	US-PR	Puerto Rico	1228
7434	US	US-RI	Rhode Island	1228
7435	US	US-SC	South Carolina	1228
7436	US	US-SD	South Dakota	1228
7437	US	US-TN	Tennessee	1228
7438	US	US-TX	Texas	1228
7439	US	US-UM	United States Minor Outlying Islands	1228
7440	US	US-UT	Utah	1228
7441	US	US-VT	Vermont	1228
7442	US	US-VI	Virgin Islands	1228
7443	US	US-VA	Virginia	1228
7444	US	US-WA	Washington	1228
7445	US	US-WV	West Virginia	1228
7446	US	US-WI	Wisconsin	1228
7447	US	US-WY	Wyoming	1228
7448	US	US-AE	Armed Forces Europe	1228
7449	US	US-AA	Armed Forces Americas	1228
7450	US	US-AP	Armed Forces Pacific	1228
7451	UY	UY-AR	Artigsa	1229
7452	UY	UY-CA	Canelones	1229
7453	UY	UY-CL	Cerro Largo	1229
7454	UY	UY-CO	Colonia	1229
7455	UY	UY-DU	Durazno	1229
7456	UY	UY-FS	Flores	1229
7457	UY	UY-LA	Lavalleja	1229
7458	UY	UY-MA	Maldonado	1229
7459	UY	UY-MO	Montevideo	1229
7460	UY	UY-PA	Paysandu	1229
7461	UY	UY-RV	Rivera	1229
7462	UY	UY-RO	Rocha	1229
7463	UY	UY-SA	Salto	1229
7464	UY	UY-SO	Soriano	1229
7465	UY	UY-TA	Tacuarembo	1229
7466	UY	UY-TT	Treinta y Tres	1229
7467	UZ	UZ-TK	Toshkent 	1230
7468	UZ	UZ-QR	Qoraqalpogiston Respublikasi	1230
7469	UZ	UZ-AN	Andijon	1230
7470	UZ	UZ-BU	Buxoro	1230
7471	UZ	UZ-FA	Farg'ona	1230
7472	UZ	UZ-JI	Jizzax	1230
7473	UZ	UZ-KH	Khorazm	1230
7474	UZ	UZ-NG	Namangan	1230
7475	UZ	UZ-NW	Navoiy	1230
7476	UZ	UZ-QA	Qashqadaryo	1230
7477	UZ	UZ-SA	Samarqand	1230
7478	UZ	UZ-SI	Sirdaryo	1230
7479	UZ	UZ-SU	Surxondaryo	1230
7480	UZ	UZ-TO	Toshkent	1230
7481	UZ	UZ-XO	Xorazm	1230
7482	VE	VE-A	Diatrito Federal	1232
7483	VE	VE-B	Anzoategui	1232
7484	VE	VE-C	Apure	1232
7485	VE	VE-D	Aragua	1232
7486	VE	VE-E	Barinas	1232
7487	VE	VE-G	Carabobo	1232
7488	VE	VE-H	Cojedes	1232
7489	VE	VE-I	Falcon	1232
7490	VE	VE-J	Guarico	1232
7491	VE	VE-K	Lara	1232
7492	VE	VE-L	Merida	1232
7493	VE	VE-M	Miranda	1232
7494	VE	VE-N	Monagas	1232
7495	VE	VE-O	Nueva Esparta	1232
7496	VE	VE-P	Portuguesa	1232
7497	VE	VE-S	Tachira	1232
7498	VE	VE-T	Trujillo	1232
7499	VE	VE-X	Vargas	1232
7500	VE	VE-U	Yaracuy	1232
7501	VE	VE-V	Zulia	1232
7502	VE	VE-Y	Delta Amacuro	1232
7503	VE	VE-W	Dependencias Federales	1232
7504	VN	VN-44	An Giang	1233
7505	VN	VN-43	Ba Ria - Vung Tau	1233
7506	VN	VN-53	Bac Can	1233
7507	VN	VN-54	Bac Giang	1233
7508	VN	VN-55	Bac Lieu	1233
7509	VN	VN-56	Bac Ninh	1233
7510	VN	VN-50	Ben Tre	1233
7511	VN	VN-31	Binh Dinh	1233
7512	VN	VN-57	Binh Duong	1233
7513	VN	VN-58	Binh Phuoc	1233
7514	VN	VN-40	Binh Thuan	1233
7515	VN	VN-59	Ca Mau	1233
7516	VN	VN-48	Can Tho	1233
7517	VN	VN-04	Cao Bang	1233
7518	VN	VN-60	Da Nang, thanh pho	1233
7520	VN	VN-39	Dong Nai	1233
7521	VN	VN-45	Dong Thap	1233
7522	VN	VN-30	Gia Lai	1233
7523	VN	VN-03	Ha Giang	1233
7524	VN	VN-63	Ha Nam	1233
7525	VN	VN-64	Ha Noi, thu do	1233
7526	VN	VN-15	Ha Tay	1233
7527	VN	VN-23	Ha Tinh	1233
7528	VN	VN-61	Hai Duong	1233
7529	VN	VN-62	Hai Phong, thanh pho	1233
7530	VN	VN-14	Hoa Binh	1233
7531	VN	VN-65	Ho Chi Minh, thanh pho [Sai Gon]	1233
7532	VN	VN-66	Hung Yen	1233
7533	VN	VN-34	Khanh Hoa	1233
7534	VN	VN-47	Kien Giang	1233
7535	VN	VN-28	Kon Tum	1233
7536	VN	VN-01	Lai Chau	1233
7537	VN	VN-35	Lam Dong	1233
7538	VN	VN-09	Lang Son	1233
7539	VN	VN-02	Lao Cai	1233
7540	VN	VN-41	Long An	1233
7541	VN	VN-67	Nam Dinh	1233
7542	VN	VN-22	Nghe An	1233
7543	VN	VN-18	Ninh Binh	1233
7544	VN	VN-36	Ninh Thuan	1233
7545	VN	VN-68	Phu Tho	1233
7546	VN	VN-32	Phu Yen	1233
7547	VN	VN-24	Quang Binh	1233
7548	VN	VN-27	Quang Nam	1233
7549	VN	VN-29	Quang Ngai	1233
7550	VN	VN-13	Quang Ninh	1233
7551	VN	VN-25	Quang Tri	1233
7552	VN	VN-52	Soc Trang	1233
7553	VN	VN-05	Son La	1233
7554	VN	VN-37	Tay Ninh	1233
7555	VN	VN-20	Thai Binh	1233
7556	VN	VN-69	Thai Nguyen	1233
7557	VN	VN-21	Thanh Hoa	1233
7558	VN	VN-26	Thua Thien-Hue	1233
7559	VN	VN-46	Tien Giang	1233
7560	VN	VN-51	Tra Vinh	1233
7561	VN	VN-07	Tuyen Quang	1233
7562	VN	VN-49	Vinh Long	1233
7563	VN	VN-70	Vinh Phuc	1233
7564	VN	VN-06	Yen Bai	1233
7565	VU	VU-MAP	Malampa	1231
7566	VU	VU-PAM	Penama	1231
7567	VU	VU-SAM	Sanma	1231
7568	VU	VU-SEE	Shefa	1231
7569	VU	VU-TAE	Tafea	1231
7570	VU	VU-TOB	Torba	1231
7571	WS	WS-AA	A'ana	1185
7572	WS	WS-AL	Aiga-i-le-Tai	1185
7573	WS	WS-AT	Atua	1185
7574	WS	WS-FA	Fa'aaaleleaga	1185
7575	WS	WS-GE	Gaga'emauga	1185
7576	WS	WS-GI	Gagaifomauga	1185
7577	WS	WS-PA	Palauli	1185
7578	WS	WS-SA	Satupa'itea	1185
7579	WS	WS-TU	Tuamasaga	1185
7580	WS	WS-VF	Va'a-o-Fonoti	1185
7581	WS	WS-VS	Vaisigano	1185
7582	CS	CS-CG	Crna Gora	1238
7583	CS	CS-SR	Srbija	1238
7584	CS	CS-KM	Kosovo-Metohija	1238
7585	CS	CS-VO	Vojvodina	1238
7586	YE	YE-AB	Abyan	1237
7587	YE	YE-AD	Adan	1237
7588	YE	YE-DA	Ad Dali	1237
7589	YE	YE-BA	Al Bayda'	1237
7590	YE	YE-MU	Al Hudaydah	1237
7591	YE	YE-MR	Al Mahrah	1237
7592	YE	YE-MW	Al Mahwit	1237
7593	YE	YE-AM	Amran	1237
7594	YE	YE-DH	Dhamar	1237
7595	YE	YE-HD	Hadramawt	1237
7596	YE	YE-HJ	Hajjah	1237
7597	YE	YE-IB	Ibb	1237
7598	YE	YE-LA	Lahij	1237
7599	YE	YE-MA	Ma'rib	1237
7600	YE	YE-SD	Sa'dah	1237
7601	YE	YE-SN	San'a'	1237
7602	YE	YE-SH	Shabwah	1237
7603	YE	YE-TA	Ta'izz	1237
7604	ZA	ZA-EC	Eastern Cape	1196
7605	ZA	ZA-FS	Free State	1196
7606	ZA	ZA-GT	Gauteng	1196
7607	ZA	ZA-NL	Kwazulu-Natal	1196
7608	ZA	ZA-MP	Mpumalanga	1196
7609	ZA	ZA-NC	Northern Cape	1196
7610	ZA	ZA-NP	Limpopo	1196
7611	ZA	ZA-WC	Western Cape	1196
7612	ZM	ZM-08	Copperbelt	1239
7613	ZM	ZM-04	Luapula	1239
7614	ZM	ZM-09	Lusaka	1239
7615	ZM	ZM-06	North-Western	1239
7616	ZW	ZW-BU	Bulawayo	1240
7617	ZW	ZW-HA	Harare	1240
7618	ZW	ZW-MA	Manicaland	1240
7619	ZW	ZW-MC	Mashonaland Central	1240
7620	ZW	ZW-ME	Mashonaland East	1240
7621	ZW	ZW-MW	Mashonaland West	1240
7622	ZW	ZW-MV	Masvingo	1240
7623	ZW	ZW-MN	Matabeleland North	1240
7624	ZW	ZW-MS	Matabeleland South	1240
7625	ZW	ZW-MI	Midlands	1240
\.


--
-- TOC entry 8 (OID 143713)
-- Name: countries_pkey; Type: CONSTRAINT; Schema: public; Owner: shot
--

ALTER TABLE ONLY countries
    ADD CONSTRAINT countries_pkey PRIMARY KEY (id);


--
-- TOC entry 9 (OID 143723)
-- Name: provinces_pkey; Type: CONSTRAINT; Schema: public; Owner: shot
--

ALTER TABLE ONLY provinces
    ADD CONSTRAINT provinces_pkey PRIMARY KEY (id);


--
-- TOC entry 7 (OID 143715)
-- Name: provinces_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shot
--

SELECT pg_catalog.setval('provinces_id_seq', 7625, true);


SET SESSION AUTHORIZATION 'postgres';

--
-- TOC entry 3 (OID 2200)
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


