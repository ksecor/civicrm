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
-- TOC entry 5 (OID 156679)
-- Name: countries; Type: TABLE; Schema: public; Owner: shot
--

CREATE TABLE countries (
    country_id integer NOT NULL,
    country_name text,
    country_iso_code text
);


--
-- TOC entry 6 (OID 156686)
-- Name: provinces; Type: TABLE; Schema: public; Owner: shot
--

CREATE TABLE provinces (
    legacy_id integer,
    country_id integer,
    province_id serial NOT NULL,
    country_iso_code text,
    province_iso_code text,
    province_name text
);


--
-- Data for TOC entry 24 (OID 156679)
-- Name: countries; Type: TABLE DATA; Schema: public; Owner: shot
--

COPY countries (country_id, country_name, country_iso_code) FROM stdin;
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
-- Data for TOC entry 25 (OID 156686)
-- Name: provinces; Type: TABLE DATA; Schema: public; Owner: shot
--

COPY provinces (legacy_id, country_id, province_id, country_iso_code, province_iso_code, province_name) FROM stdin;
1019	1228	7626	US	US-MD	Maryland
1025	1228	7627	US	US-MT	Montana
1000	1228	7393	US	US-AL	Alabama
1001	1228	7394	US	US-AK	Alaska
1002	1228	7396	US	US-AZ	Arizona
1003	1228	7397	US	US-AR	Arkansas
1004	1228	7398	US	US-CA	California
1005	1228	7399	US	US-CO	Colorado
1006	1228	7400	US	US-CT	Connecticut
1007	1228	7401	US	US-DE	Delaware
1008	1228	7403	US	US-FL	Florida
1009	1228	7404	US	US-GA	Georgia
1010	1228	7406	US	US-HI	Hawaii
1011	1228	7407	US	US-ID	Idaho
1012	1228	7408	US	US-IL	Illinois
1013	1228	7409	US	US-IN	Indiana
1015	1228	7411	US	US-KS	Kansas
1016	1228	7412	US	US-KY	Kentucky
1017	1228	7413	US	US-LA	Louisiana
1018	1228	7414	US	US-ME	Maine
1020	1228	7415	US	US-MA	Massachusetts
1021	1228	7416	US	US-MI	Michigan
1022	1228	7417	US	US-MN	Minnesota
1023	1228	7418	US	US-MS	Mississippi
1024	1228	7419	US	US-MO	Missouri
1026	1228	7420	US	US-NE	Nebraska
1027	1228	7421	US	US-NV	Nevada
1028	1228	7422	US	US-NH	New Hampshire
1029	1228	7423	US	US-NJ	New Jersey
1030	1228	7424	US	US-NM	New Mexico
1031	1228	7425	US	US-NY	New York
1032	1228	7426	US	US-NC	North Carolina
1033	1228	7427	US	US-ND	North Dakota
1034	1228	7429	US	US-OH	Ohio
1035	1228	7430	US	US-OK	Oklahoma
1036	1228	7431	US	US-OR	Oregon
1037	1228	7432	US	US-PA	Pennsylvania
1038	1228	7434	US	US-RI	Rhode Island
1039	1228	7435	US	US-SC	South Carolina
1040	1228	7436	US	US-SD	South Dakota
1041	1228	7437	US	US-TN	Tennessee
1042	1228	7438	US	US-TX	Texas
1043	1228	7440	US	US-UT	Utah
1044	1228	7441	US	US-VT	Vermont
1045	1228	7443	US	US-VA	Virginia
1046	1228	7444	US	US-WA	Washington
1047	1228	7445	US	US-WV	West Virginia
1048	1228	7446	US	US-WI	Wisconsin
1049	1228	7447	US	US-WY	Wyoming
1050	1228	7402	US	US-DC	District of Columbia
1052	1228	7395	US	US-AS	American Samoa
1053	1228	7405	US	US-GU	Guam
1055	1228	7428	US	US-MP	Northern Mariana Islands
1056	1228	7433	US	US-PR	Puerto Rico
1057	1228	7442	US	US-VI	Virgin Islands
1100	1039	4356	CA	CA-AB	Alberta
1101	1039	4357	CA	CA-BC	British Columbia
1102	1039	4358	CA	CA-MB	Manitoba
1103	1039	4359	CA	CA-NB	New Brunswick
1104	1039	4360	CA	CA-NL	Newfoundland and Labrador
1105	1039	4366	CA	CA-NT	Northwest Territories
1106	1039	4361	CA	CA-NS	Nova Scotia
1107	1039	4367	CA	CA-NU	Nunavut
1108	1039	4362	CA	CA-ON	Ontario
1109	1039	4363	CA	CA-PE	Prince Edward Island
1110	1039	4364	CA	CA-QC	Quebec
1111	1039	4365	CA	CA-SK	Saskatchewan
1112	1039	4368	CA	CA-YT	Yukon Territory
1200	1101	5493	IN	IN-MM	Maharashtra
1201	1101	5490	IN	IN-KA	Karnataka
1300	1172	6527	PL	PL-MZ	Mazowieckie
1301	1172	6531	PL	PL-PM	Pomorskie
\N	1225	3849	AE	AE-AZ	Abu Zaby
\N	1225	3850	AE	AE-AJ	'Ajman
\N	1225	3851	AE	AE-FU	Al Fujayrah
\N	1225	3852	AE	AE-SH	Ash Shariqah
\N	1225	3853	AE	AE-DU	Dubayy
\N	1225	3854	AE	AE-RK	Ra's al Khaymah
\N	1233	7519	VN	VN-33	Dac Lac
\N	1225	3855	AE	AE-UQ	Umm al Qaywayn
\N	1001	3856	AF	AF-BDS	Badakhshan
\N	1001	3857	AF	AF-BDG	Badghis
\N	1001	3858	AF	AF-BGL	Baghlan
\N	1001	3859	AF	AF-BAL	Balkh
\N	1001	3860	AF	AF-BAM	Bamian
\N	1001	3861	AF	AF-FRA	Farah
\N	1001	3862	AF	AF-FYB	Faryab
\N	1001	3863	AF	AF-GHA	Ghazni
\N	1001	3864	AF	AF-GHO	Ghowr
\N	1001	3865	AF	AF-HEL	Helmand
\N	1001	3866	AF	AF-HER	Herat
\N	1001	3867	AF	AF-JOW	Jowzjan
\N	1001	3868	AF	AF-KAB	Kabul
\N	1001	3869	AF	AF-KAN	Kandahar
\N	1001	3870	AF	AF-KAP	Kapisa
\N	1001	3871	AF	AF-KHO	Khowst
\N	1001	3872	AF	AF-KNR	Konar
\N	1001	3873	AF	AF-KDZ	Kondoz
\N	1001	3874	AF	AF-LAG	Laghman
\N	1001	3875	AF	AF-LOW	Lowgar
\N	1001	3876	AF	AF-NAN	Nangrahar
\N	1001	3877	AF	AF-NIM	Nimruz
\N	1001	3878	AF	AF-NUR	Nurestan
\N	1001	3879	AF	AF-ORU	Oruzgan
\N	1001	3880	AF	AF-PIA	Paktia
\N	1001	3881	AF	AF-PKA	Paktika
\N	1001	3882	AF	AF-PAR	Parwan
\N	1001	3883	AF	AF-SAM	Samangan
\N	1001	3884	AF	AF-SAR	Sar-e Pol
\N	1001	3885	AF	AF-TAK	Takhar
\N	1001	3886	AF	AF-WAR	Wardak
\N	1001	3887	AF	AF-ZAB	Zabol
\N	1002	3888	AL	AL-BR	Berat
\N	1002	3889	AL	AL-BU	Bulqizë
\N	1002	3890	AL	AL-DL	Delvinë
\N	1002	3891	AL	AL-DV	Devoll
\N	1002	3892	AL	AL-DI	Dibër
\N	1002	3893	AL	AL-DR	Durrsës
\N	1002	3894	AL	AL-EL	Elbasan
\N	1002	3895	AL	AL-FR	Fier
\N	1002	3896	AL	AL-GR	Gramsh
\N	1002	3897	AL	AL-GJ	Gjirokastër
\N	1002	3898	AL	AL-HA	Has
\N	1002	3899	AL	AL-KA	Kavajë
\N	1002	3900	AL	AL-ER	Kolonjë
\N	1002	3901	AL	AL-KO	Korcë
\N	1002	3902	AL	AL-KR	Krujë
\N	1002	3903	AL	AL-KC	Kuçovë
\N	1002	3904	AL	AL-KU	Kukës
\N	1002	3905	AL	AL-KB	Kurbin
\N	1002	3906	AL	AL-LE	Lezhë
\N	1002	3907	AL	AL-LB	Librazhd
\N	1002	3908	AL	AL-LU	Lushnjë
\N	1002	3909	AL	AL-MM	Malësi e Madhe
\N	1002	3910	AL	AL-MK	Mallakastër
\N	1002	3911	AL	AL-MT	Mat
\N	1002	3912	AL	AL-MR	Mirditë
\N	1002	3913	AL	AL-PQ	Peqin
\N	1002	3914	AL	AL-PR	Përmet
\N	1002	3915	AL	AL-PG	Pogradec
\N	1002	3916	AL	AL-PU	Pukë
\N	1002	3917	AL	AL-SR	Sarandë
\N	1002	3918	AL	AL-SK	Skrapar
\N	1002	3919	AL	AL-SH	Shkodër
\N	1002	3920	AL	AL-TE	Tepelenë
\N	1002	3921	AL	AL-TR	Tiranë
\N	1002	3922	AL	AL-TP	Tropojë
\N	1002	3923	AL	AL-VL	Vlorë
\N	1011	3924	AM	AM-ER	Erevan
\N	1011	3925	AM	AM-AG	Aragacotn
\N	1011	3926	AM	AM-AR	Ararat
\N	1011	3927	AM	AM-AV	Armavir
\N	1011	3928	AM	AM-GR	Gegarkunik'
\N	1011	3929	AM	AM-KT	Kotayk'
\N	1011	3930	AM	AM-LO	Lory
\N	1011	3931	AM	AM-SH	Sirak
\N	1011	3932	AM	AM-SU	Syunik'
\N	1011	3933	AM	AM-TV	Tavus
\N	1011	3934	AM	AM-VD	Vayoc Jor
\N	1006	3935	AO	AO-BGO	Bengo
\N	1006	3936	AO	AO-BGU	Benguela
\N	1006	3937	AO	AO-BIE	Bie
\N	1006	3938	AO	AO-CAB	Cabinda
\N	1006	3939	AO	AO-CCU	Cuando-Cubango
\N	1006	3940	AO	AO-CNO	Cuanza Norte
\N	1006	3941	AO	AO-CUS	Cuanza Sul
\N	1006	3942	AO	AO-CNN	Cunene
\N	1006	3943	AO	AO-HUA	Huambo
\N	1006	3944	AO	AO-HUI	Huila
\N	1006	3945	AO	AO-LUA	Luanda
\N	1006	3946	AO	AO-LNO	Lunda Norte
\N	1006	3947	AO	AO-LSU	Lunda Sul
\N	1006	3948	AO	AO-MAL	Malange
\N	1006	3949	AO	AO-MOX	Moxico
\N	1006	3950	AO	AO-NAM	Namibe
\N	1006	3951	AO	AO-UIG	Uige
\N	1006	3952	AO	AO-ZAI	Zaire
\N	1010	3953	AR	AR-C	Capital federal
\N	1010	3954	AR	AR-B	Buenos Aires
\N	1010	3955	AR	AR-K	Catamarca
\N	1010	3956	AR	AR-X	Cordoba
\N	1010	3957	AR	AR-W	Corrientes
\N	1010	3958	AR	AR-H	Chaco
\N	1010	3959	AR	AR-U	Chubut
\N	1010	3960	AR	AR-E	Entre Rios
\N	1010	3961	AR	AR-P	Formosa
\N	1010	3962	AR	AR-Y	Jujuy
\N	1010	3963	AR	AR-L	La Pampa
\N	1010	3964	AR	AR-M	Mendoza
\N	1010	3965	AR	AR-N	Misiones
\N	1010	3966	AR	AR-Q	Neuquen
\N	1010	3967	AR	AR-R	Rio Negro
\N	1010	3968	AR	AR-A	Salta
\N	1010	3969	AR	AR-J	San Juan
\N	1010	3970	AR	AR-D	San Luis
\N	1010	3971	AR	AR-Z	Santa Cruz
\N	1010	3972	AR	AR-S	Santa Fe
\N	1010	3973	AR	AR-G	Santiago del Estero
\N	1010	3974	AR	AR-V	Tierra del Fuego
\N	1010	3975	AR	AR-T	Tucuman
\N	1014	3976	AT	AT-1	Burgenland
\N	1014	3977	AT	AT-2	Kärnten
\N	1014	3978	AT	AT-3	Niederosterreich
\N	1014	3979	AT	AT-4	Oberosterreich
\N	1014	3980	AT	AT-5	Salzburg
\N	1014	3981	AT	AT-6	Steiermark
\N	1014	3982	AT	AT-7	Tirol
\N	1014	3983	AT	AT-8	Vorarlberg
\N	1014	3984	AT	AT-9	Wien
\N	1013	3985	AU	AU-AAT	Australian Antarctic Territory
\N	1013	3986	AU	AU-ACT	Australian Capital Territory
\N	1013	3987	AU	AU-NT	Northern Territory
\N	1013	3988	AU	AU-NSW	New South Wales
\N	1013	3989	AU	AU-QLD	Queensland
\N	1013	3990	AU	AU-SA	South Australia
\N	1013	3991	AU	AU-TAS	Tasmania
\N	1013	3992	AU	AU-VIC	Victoria
\N	1013	3993	AU	AU-WA	Western Australia
\N	1015	3994	AZ	AZ-NX	Naxcivan
\N	1015	3995	AZ	AZ-AB	Ali Bayramli
\N	1015	3996	AZ	AZ-BA	Baki
\N	1015	3997	AZ	AZ-GA	Ganca
\N	1015	3998	AZ	AZ-LA	Lankaran
\N	1015	3999	AZ	AZ-MI	Mingacevir
\N	1015	4000	AZ	AZ-NA	Naftalan
\N	1015	4001	AZ	AZ-SA	Saki
\N	1015	4002	AZ	AZ-SM	Sumqayit
\N	1015	4003	AZ	AZ-SS	Susa
\N	1015	4004	AZ	AZ-XA	Xankandi
\N	1015	4005	AZ	AZ-YE	Yevlax
\N	1015	4006	AZ	AZ-ABS	Abseron
\N	1015	4007	AZ	AZ-AGC	Agcabadi
\N	1015	4008	AZ	AZ-AGM	Agdam
\N	1015	4009	AZ	AZ-AGS	Agdas
\N	1015	4010	AZ	AZ-AGA	Agstafa
\N	1015	4011	AZ	AZ-AGU	Agsu
\N	1015	4012	AZ	AZ-AST	Astara
\N	1015	4013	AZ	AZ-BAB	Babak
\N	1015	4014	AZ	AZ-BAL	Balakan
\N	1015	4015	AZ	AZ-BAR	Barda
\N	1015	4016	AZ	AZ-BEY	Beylagan
\N	1015	4017	AZ	AZ-BIL	Bilasuvar
\N	1015	4018	AZ	AZ-CAB	Cabrayll
\N	1015	4019	AZ	AZ-CAL	Calilabad
\N	1015	4020	AZ	AZ-CUL	Culfa
\N	1015	4021	AZ	AZ-DAS	Daskasan
\N	1015	4022	AZ	AZ-DAV	Davaci
\N	1015	4023	AZ	AZ-FUZ	Fuzuli
\N	1015	4024	AZ	AZ-GAD	Gadabay
\N	1015	4025	AZ	AZ-GOR	Goranboy
\N	1015	4026	AZ	AZ-GOY	Goycay
\N	1015	4027	AZ	AZ-HAC	Haciqabul
\N	1015	4028	AZ	AZ-IMI	Imisli
\N	1015	4029	AZ	AZ-ISM	Ismayilli
\N	1015	4030	AZ	AZ-KAL	Kalbacar
\N	1015	4031	AZ	AZ-KUR	Kurdamir
\N	1015	4032	AZ	AZ-LAC	Lacin
\N	1015	4033	AZ	AZ-LER	Lerik
\N	1015	4034	AZ	AZ-MAS	Masalli
\N	1015	4035	AZ	AZ-NEF	Neftcala
\N	1015	4036	AZ	AZ-OGU	Oguz
\N	1015	4037	AZ	AZ-ORD	Ordubad
\N	1015	4038	AZ	AZ-QAB	Qabala
\N	1015	4039	AZ	AZ-QAX	Qax
\N	1015	4040	AZ	AZ-QAZ	Qazax
\N	1015	4041	AZ	AZ-QOB	Qobustan
\N	1015	4042	AZ	AZ-QBA	Quba
\N	1015	4043	AZ	AZ-QBI	Qubadli
\N	1015	4044	AZ	AZ-QUS	Qusar
\N	1015	4045	AZ	AZ-SAT	Saatli
\N	1015	4046	AZ	AZ-SAB	Sabirabad
\N	1015	4047	AZ	AZ-SAD	Sadarak
\N	1015	4048	AZ	AZ-SAH	Sahbuz
\N	1015	4049	AZ	AZ-SAL	Salyan
\N	1015	4050	AZ	AZ-SMI	Samaxi
\N	1015	4051	AZ	AZ-SKR	Samkir
\N	1015	4052	AZ	AZ-SMX	Samux
\N	1015	4053	AZ	AZ-SAR	Sarur
\N	1015	4054	AZ	AZ-SIY	Siyazan
\N	1015	4055	AZ	AZ-TAR	Tartar
\N	1015	4056	AZ	AZ-TOV	Tovuz
\N	1015	4057	AZ	AZ-UCA	Ucar
\N	1015	4058	AZ	AZ-XAC	Xacmaz
\N	1015	4059	AZ	AZ-XAN	Xanlar
\N	1015	4060	AZ	AZ-XIZ	Xizi
\N	1015	4061	AZ	AZ-XCI	Xocali
\N	1015	4062	AZ	AZ-XVD	Xocavand
\N	1015	4063	AZ	AZ-YAR	Yardimli
\N	1015	4064	AZ	AZ-ZAN	Zangilan
\N	1015	4065	AZ	AZ-ZAQ	Zaqatala
\N	1015	4066	AZ	AZ-ZAR	Zardab
\N	1026	4067	BA	BA-BIH	Federacija Bosna i Hercegovina
\N	1026	4068	BA	BA-SRP	Republika Srpska
\N	1017	4069	BD	BD-05	Bagerhat zila
\N	1017	4070	BD	BD-01	Bandarban zila
\N	1017	4071	BD	BD-02	Barguna zila
\N	1017	4072	BD	BD-06	Barisal zila
\N	1017	4073	BD	BD-07	Bhola zila
\N	1017	4074	BD	BD-03	Bogra zila
\N	1017	4075	BD	BD-04	Brahmanbaria zila
\N	1017	4076	BD	BD-09	Chandpur zila
\N	1017	4077	BD	BD-10	Chittagong zila
\N	1017	4078	BD	BD-12	Chuadanga zila
\N	1017	4079	BD	BD-08	Comilla zila
\N	1017	4080	BD	BD-11	Cox's Bazar zila
\N	1017	4081	BD	BD-13	Dhaka zila
\N	1017	4082	BD	BD-14	Dinajpur zila
\N	1017	4083	BD	BD-15	Faridpur zila
\N	1017	4084	BD	BD-16	Feni zila
\N	1017	4085	BD	BD-19	Gaibandha zila
\N	1017	4086	BD	BD-18	Gazipur zila
\N	1017	4087	BD	BD-17	Gopalganj zila
\N	1017	4088	BD	BD-20	Habiganj zila
\N	1017	4089	BD	BD-24	Jaipurhat zila
\N	1017	4090	BD	BD-21	Jamalpur zila
\N	1017	4091	BD	BD-22	Jessore zila
\N	1017	4092	BD	BD-25	Jhalakati zila
\N	1017	4093	BD	BD-23	Jhenaidah zila
\N	1017	4094	BD	BD-29	Khagrachari zila
\N	1017	4095	BD	BD-27	Khulna zila
\N	1017	4096	BD	BD-26	Kishorganj zila
\N	1017	4097	BD	BD-28	Kurigram zila
\N	1017	4098	BD	BD-30	Kushtia zila
\N	1017	4099	BD	BD-31	Lakshmipur zila
\N	1017	4100	BD	BD-32	Lalmonirhat zila
\N	1017	4101	BD	BD-36	Madaripur zila
\N	1017	4102	BD	BD-37	Magura zila
\N	1017	4103	BD	BD-33	Manikganj zila
\N	1017	4104	BD	BD-39	Meherpur zila
\N	1017	4105	BD	BD-38	Moulvibazar zila
\N	1017	4106	BD	BD-35	Munshiganj zila
\N	1017	4107	BD	BD-34	Mymensingh zila
\N	1017	4108	BD	BD-48	Naogaon zila
\N	1017	4109	BD	BD-43	Narail zila
\N	1017	4110	BD	BD-40	Narayanganj zila
\N	1017	4111	BD	BD-42	Narsingdi zila
\N	1017	4112	BD	BD-44	Natore zila
\N	1017	4113	BD	BD-45	Nawabganj zila
\N	1017	4114	BD	BD-41	Netrakona zila
\N	1017	4115	BD	BD-46	Nilphamari zila
\N	1017	4116	BD	BD-47	Noakhali zila
\N	1017	4117	BD	BD-49	Pabna zila
\N	1017	4118	BD	BD-52	Panchagarh zila
\N	1017	4119	BD	BD-51	Patuakhali zila
\N	1017	4120	BD	BD-50	Pirojpur zila
\N	1017	4121	BD	BD-53	Rajbari zila
\N	1017	4122	BD	BD-54	Rajshahi zila
\N	1017	4123	BD	BD-56	Rangamati zila
\N	1017	4124	BD	BD-55	Rangpur zila
\N	1017	4125	BD	BD-58	Satkhira zila
\N	1017	4126	BD	BD-62	Shariatpur zila
\N	1017	4127	BD	BD-57	Sherpur zila
\N	1017	4128	BD	BD-59	Sirajganj zila
\N	1017	4129	BD	BD-61	Sunamganj zila
\N	1017	4130	BD	BD-60	Sylhet zila
\N	1017	4131	BD	BD-63	Tangail zila
\N	1017	4132	BD	BD-64	Thakurgaon zila
\N	1020	4133	BE	BE-VAN	Antwerpen
\N	1020	4134	BE	BE-WBR	Brabant Wallon
\N	1020	4135	BE	BE-WHT	Hainaut
\N	1020	4136	BE	BE-WLG	Liege
\N	1020	4137	BE	BE-VLI	Limburg
\N	1020	4138	BE	BE-WLX	Luxembourg
\N	1020	4139	BE	BE-WNA	Namur
\N	1020	4140	BE	BE-VOV	Oost-Vlaanderen
\N	1020	4141	BE	BE-VBR	Vlaams-Brabant
\N	1020	4142	BE	BE-VWV	West-Vlaanderen
\N	1034	4143	BF	BF-BAL	Bale
\N	1034	4144	BF	BF-BAM	Bam
\N	1034	4145	BF	BF-BAN	Banwa
\N	1034	4146	BF	BF-BAZ	Bazega
\N	1034	4147	BF	BF-BGR	Bougouriba
\N	1034	4148	BF	BF-BLG	Boulgou
\N	1034	4149	BF	BF-BLK	Boulkiemde
\N	1034	4150	BF	BF-COM	Comoe
\N	1034	4151	BF	BF-GAN	Ganzourgou
\N	1034	4152	BF	BF-GNA	Gnagna
\N	1034	4153	BF	BF-GOU	Gourma
\N	1034	4154	BF	BF-HOU	Houet
\N	1034	4155	BF	BF-IOB	Ioba
\N	1034	4156	BF	BF-KAD	Kadiogo
\N	1034	4157	BF	BF-KEN	Kenedougou
\N	1034	4158	BF	BF-KMD	Komondjari
\N	1034	4159	BF	BF-KMP	Kompienga
\N	1034	4160	BF	BF-KOS	Kossi
\N	1034	4161	BF	BF-KOP	Koulpulogo
\N	1034	4162	BF	BF-KOT	Kouritenga
\N	1034	4163	BF	BF-KOW	Kourweogo
\N	1034	4164	BF	BF-LER	Leraba
\N	1034	4165	BF	BF-LOR	Loroum
\N	1034	4166	BF	BF-MOU	Mouhoun
\N	1034	4167	BF	BF-NAO	Nahouri
\N	1034	4168	BF	BF-NAM	Namentenga
\N	1034	4169	BF	BF-NAY	Nayala
\N	1034	4170	BF	BF-NOU	Noumbiel
\N	1034	4171	BF	BF-OUB	Oubritenga
\N	1034	4172	BF	BF-OUD	Oudalan
\N	1034	4173	BF	BF-PAS	Passore
\N	1034	4174	BF	BF-PON	Poni
\N	1034	4175	BF	BF-SNG	Sanguie
\N	1034	4176	BF	BF-SMT	Sanmatenga
\N	1034	4177	BF	BF-SEN	Seno
\N	1034	4178	BF	BF-SIS	Siasili
\N	1034	4179	BF	BF-SOM	Soum
\N	1034	4180	BF	BF-SOR	Sourou
\N	1034	4181	BF	BF-TAP	Tapoa
\N	1034	4182	BF	BF-TUI	Tui
\N	1034	4183	BF	BF-YAG	Yagha
\N	1034	4184	BF	BF-YAT	Yatenga
\N	1034	4185	BF	BF-ZIR	Ziro
\N	1034	4186	BF	BF-ZON	Zondoma
\N	1034	4187	BF	BF-ZOU	Zoundweogo
\N	1033	4188	BG	BG-01	Blagoevgrad
\N	1033	4189	BG	BG-02	Burgas
\N	1033	4190	BG	BG-08	Dobric
\N	1033	4191	BG	BG-07	Gabrovo
\N	1033	4192	BG	BG-26	Haskovo
\N	1033	4193	BG	BG-28	Jambol
\N	1033	4194	BG	BG-09	Kardzali
\N	1033	4195	BG	BG-10	Kjstendil
\N	1033	4196	BG	BG-11	Lovec
\N	1033	4197	BG	BG-12	Montana
\N	1033	4198	BG	BG-13	Pazardzik
\N	1033	4199	BG	BG-14	Pernik
\N	1033	4200	BG	BG-15	Pleven
\N	1033	4201	BG	BG-16	Plovdiv
\N	1033	4202	BG	BG-17	Razgrad
\N	1033	4203	BG	BG-18	Ruse
\N	1033	4204	BG	BG-19	Silistra
\N	1033	4205	BG	BG-20	Sliven
\N	1033	4206	BG	BG-21	Smoljan
\N	1033	4207	BG	BG-23	Sofija
\N	1033	4208	BG	BG-24	Stara Zagora
\N	1033	4209	BG	BG-27	Sumen
\N	1033	4210	BG	BG-25	Targoviste
\N	1033	4211	BG	BG-03	Varna
\N	1033	4212	BG	BG-04	Veliko Tarnovo
\N	1033	4213	BG	BG-05	Vidin
\N	1033	4214	BG	BG-06	Vraca
\N	1016	4215	BH	BH-01	Al Hadd
\N	1016	4216	BH	BH-03	Al Manamah
\N	1016	4217	BH	BH-10	Al Mintaqah al Gharbiyah
\N	1016	4218	BH	BH-07	Al Mintagah al Wusta
\N	1016	4219	BH	BH-05	Al Mintaqah ash Shamaliyah
\N	1016	4220	BH	BH-02	Al Muharraq
\N	1016	4221	BH	BH-09	Ar Rifa
\N	1016	4222	BH	BH-04	Jidd Hafs
\N	1016	4223	BH	BH-12	Madluat Jamad
\N	1016	4224	BH	BH-08	Madluat Isa
\N	1016	4225	BH	BH-11	Mintaqat Juzur tawar
\N	1016	4226	BH	BH-06	Sitrah
\N	1036	4227	BI	BI-BB	Bubanza
\N	1036	4228	BI	BI-BJ	Bujumbura
\N	1036	4229	BI	BI-BR	Bururi
\N	1036	4230	BI	BI-CA	Cankuzo
\N	1036	4231	BI	BI-CI	Cibitoke
\N	1036	4232	BI	BI-GI	Gitega
\N	1036	4233	BI	BI-KR	Karuzi
\N	1036	4234	BI	BI-KY	Kayanza
\N	1036	4235	BI	BI-MA	Makamba
\N	1036	4236	BI	BI-MU	Muramvya
\N	1036	4237	BI	BI-MW	Mwaro
\N	1036	4238	BI	BI-NG	Ngozi
\N	1036	4239	BI	BI-RT	Rutana
\N	1036	4240	BI	BI-RY	Ruyigi
\N	1022	4241	BJ	BJ-AL	Alibori
\N	1022	4242	BJ	BJ-AK	Atakora
\N	1022	4243	BJ	BJ-AQ	Atlantique
\N	1022	4244	BJ	BJ-BO	Borgou
\N	1022	4245	BJ	BJ-CO	Collines
\N	1022	4246	BJ	BJ-DO	Donga
\N	1022	4247	BJ	BJ-KO	Kouffo
\N	1022	4248	BJ	BJ-LI	Littoral
\N	1022	4249	BJ	BJ-MO	Mono
\N	1022	4250	BJ	BJ-OU	Oueme
\N	1022	4251	BJ	BJ-PL	Plateau
\N	1022	4252	BJ	BJ-ZO	Zou
\N	1032	4253	BN	BN-BE	Belait
\N	1032	4254	BN	BN-BM	Brunei-Muara
\N	1032	4255	BN	BN-TE	Temburong
\N	1032	4256	BN	BN-TU	Tutong
\N	1025	4257	BO	BO-C	Cochabamba
\N	1025	4258	BO	BO-H	Chuquisaca
\N	1025	4259	BO	BO-B	El Beni
\N	1025	4260	BO	BO-L	La Paz
\N	1025	4261	BO	BO-O	Oruro
\N	1025	4262	BO	BO-N	Pando
\N	1025	4263	BO	BO-P	Potosi
\N	1025	4264	BO	BO-T	Tarija
\N	1029	4265	BR	BR-AC	Acre
\N	1029	4266	BR	BR-AL	Alagoas
\N	1029	4267	BR	BR-AM	Amazonas
\N	1029	4268	BR	BR-AP	Amapa
\N	1029	4269	BR	BR-BA	Baia
\N	1029	4270	BR	BR-CE	Ceara
\N	1029	4271	BR	BR-DF	Distrito Federal
\N	1029	4272	BR	BR-ES	Espirito Santo
\N	1029	4273	BR	BR-FN	Fernando de Noronha
\N	1029	4274	BR	BR-GO	Goias
\N	1029	4275	BR	BR-MA	Maranhao
\N	1029	4276	BR	BR-MG	Minas Gerais
\N	1029	4277	BR	BR-MS	Mato Grosso do Sul
\N	1029	4278	BR	BR-MT	Mato Grosso
\N	1029	4279	BR	BR-PA	Para
\N	1029	4280	BR	BR-PB	Paraiba
\N	1029	4281	BR	BR-PE	Pernambuco
\N	1029	4282	BR	BR-PI	Piaui
\N	1029	4283	BR	BR-PR	Parana
\N	1029	4284	BR	BR-RJ	Rio de Janeiro
\N	1029	4285	BR	BR-RN	Rio Grande do Norte
\N	1029	4286	BR	BR-RO	Rondonia
\N	1029	4287	BR	BR-RR	Roraima
\N	1029	4288	BR	BR-RS	Rio Grande do Sul
\N	1029	4289	BR	BR-SC	Santa Catarina
\N	1029	4290	BR	BR-SE	Sergipe
\N	1029	4291	BR	BR-SP	Sao Paulo
\N	1029	4292	BR	BR-TO	Tocatins
\N	1212	4293	BS	BS-AC	Acklins and Crooked Islands
\N	1212	4294	BS	BS-BI	Bimini
\N	1212	4295	BS	BS-CI	Cat Island
\N	1212	4296	BS	BS-EX	Exuma
\N	1212	4297	BS	BS-FP	Freeport
\N	1212	4298	BS	BS-FC	Fresh Creek
\N	1212	4299	BS	BS-GH	Governor's Harbour
\N	1212	4300	BS	BS-GT	Green Turtle Cay
\N	1212	4301	BS	BS-HI	Harbour Island
\N	1212	4302	BS	BS-HR	High Rock
\N	1212	4303	BS	BS-IN	Inagua
\N	1212	4304	BS	BS-KB	Kemps Bay
\N	1212	4305	BS	BS-LI	Long Island
\N	1212	4306	BS	BS-MH	Marsh Harbour
\N	1212	4307	BS	BS-MG	Mayaguana
\N	1212	4308	BS	BS-NP	New Providence
\N	1212	4309	BS	BS-NB	Nicholls Town and Berry Islands
\N	1212	4310	BS	BS-RI	Ragged Island
\N	1212	4311	BS	BS-RS	Rock Sound
\N	1212	4312	BS	BS-SP	Sandy Point
\N	1212	4313	BS	BS-SR	San Salvador and Rum Cay
\N	1024	4314	BT	BT-33	Bumthang
\N	1024	4315	BT	BT-12	Chhukha
\N	1024	4316	BT	BT-22	Dagana
\N	1024	4317	BT	BT-GA	Gasa
\N	1024	4318	BT	BT-13	Ha
\N	1024	4319	BT	BT-44	Lhuentse
\N	1024	4320	BT	BT-42	Monggar
\N	1024	4321	BT	BT-11	Paro
\N	1024	4322	BT	BT-43	Pemagatshel
\N	1024	4323	BT	BT-23	Punakha
\N	1024	4324	BT	BT-45	Samdrup Jongkha
\N	1024	4325	BT	BT-14	Samtee
\N	1024	4326	BT	BT-31	Sarpang
\N	1024	4327	BT	BT-15	Thimphu
\N	1024	4328	BT	BT-41	Trashigang
\N	1024	4329	BT	BT-TY	Trashi Yangtse
\N	1024	4330	BT	BT-32	Trongsa
\N	1024	4331	BT	BT-21	Tsirang
\N	1024	4332	BT	BT-24	Wangdue Phodrang
\N	1024	4333	BT	BT-34	Zhemgang
\N	1027	4334	BW	BW-CE	Central
\N	1027	4335	BW	BW-GH	Ghanzi
\N	1027	4336	BW	BW-KG	Kgalagadi
\N	1027	4337	BW	BW-KL	Kgatleng
\N	1027	4338	BW	BW-KW	Kweneng
\N	1027	4339	BW	BW-NG	Ngamiland
\N	1027	4340	BW	BW-NE	North-East
\N	1027	4341	BW	BW-NW	North-West
\N	1027	4342	BW	BW-SE	South-East
\N	1027	4343	BW	BW-SO	Southern
\N	1019	4344	BY	BY-BR	Brèsckaja voblasc'
\N	1019	4345	BY	BY-HO	Homel'skaja voblasc'
\N	1019	4346	BY	BY-HR	Hrodzenskaja voblasc'
\N	1019	4347	BY	BY-MA	Mahilëuskaja voblasc'
\N	1019	4348	BY	BY-MI	Minskaja voblasc'
\N	1019	4349	BY	BY-VI	Vicebskaja voblasc'
\N	1021	4350	BZ	BZ-BZ	Belize
\N	1021	4351	BZ	BZ-CY	Cayo
\N	1021	4352	BZ	BZ-CZL	Corozal
\N	1021	4353	BZ	BZ-OW	Orange Walk
\N	1021	4354	BZ	BZ-SC	Stann Creek
\N	1021	4355	BZ	BZ-TOL	Toledo
\N	1050	4369	CD	CD-KN	Kinshasa
\N	1050	4370	CD	CD-BN	Bandundu
\N	1050	4371	CD	CD-BC	Bas-Congo
\N	1050	4372	CD	CD-EQ	Equateur
\N	1050	4373	CD	CD-HC	Haut-Congo
\N	1050	4374	CD	CD-KW	Kasai-Occidental
\N	1050	4375	CD	CD-KE	Kasai-Oriental
\N	1050	4376	CD	CD-KA	Katanga
\N	1050	4377	CD	CD-MA	Maniema
\N	1050	4378	CD	CD-NK	Nord-Kivu
\N	1050	4379	CD	CD-OR	Orientale
\N	1050	4380	CD	CD-SK	Sud-Kivu
\N	1042	4381	CF	CF-BGF	Bangui
\N	1042	4382	CF	CF-BB	Bamingui-Bangoran
\N	1042	4383	CF	CF-BK	Basse-Kotto
\N	1042	4384	CF	CF-HK	Haute-Kotto
\N	1042	4385	CF	CF-HM	Haut-Mbomou
\N	1042	4386	CF	CF-KG	Kemo
\N	1042	4387	CF	CF-LB	Lobaye
\N	1042	4388	CF	CF-HS	Mambere-Kadei
\N	1042	4389	CF	CF-MB	Mbomou
\N	1042	4390	CF	CF-KB	Nana-Grebizi
\N	1042	4391	CF	CF-NM	Nana-Mambere
\N	1042	4392	CF	CF-MP	Ombella-Mpoko
\N	1042	4393	CF	CF-UK	Ouaka
\N	1042	4394	CF	CF-AC	Ouham
\N	1042	4395	CF	CF-OP	Ouham-Pende
\N	1042	4396	CF	CF-SE	Sangha-Mbaere
\N	1042	4397	CF	CF-VR	Vakaga
\N	1051	4398	CG	CG-BZV	Brazzaville
\N	1051	4399	CG	CG-11	Bouenza
\N	1051	4400	CG	CG-8	Cuvette
\N	1051	4401	CG	CG-15	Cuvette-Ouest
\N	1051	4402	CG	CG-5	Kouilou
\N	1051	4403	CG	CG-2	Lekoumou
\N	1051	4404	CG	CG-7	Likouala
\N	1051	4405	CG	CG-9	Niari
\N	1051	4406	CG	CG-14	Plateaux
\N	1051	4407	CG	CG-12	Pool
\N	1051	4408	CG	CG-13	Sangha
\N	1205	4409	CH	CH-AG	Aargau
\N	1205	4410	CH	CH-AI	Appenzell Innerrhoden
\N	1205	4411	CH	CH-AR	Appenzell Ausserrhoden
\N	1205	4412	CH	CH-BE	Bern
\N	1205	4413	CH	CH-BL	Basel-Landschaft
\N	1205	4414	CH	CH-BS	Basel-Stadt
\N	1205	4415	CH	CH-FR	Fribourg
\N	1205	4416	CH	CH-GE	Geneva
\N	1205	4417	CH	CH-GL	Glarus
\N	1205	4418	CH	CH-GR	Graubunden
\N	1205	4419	CH	CH-JU	Jura
\N	1205	4420	CH	CH-LU	Luzern
\N	1205	4421	CH	CH-NE	Neuchatel
\N	1205	4422	CH	CH-NW	Nidwalden
\N	1205	4423	CH	CH-OW	Obwalden
\N	1205	4424	CH	CH-SG	Sankt Gallen
\N	1205	4425	CH	CH-SH	Schaffhausen
\N	1205	4426	CH	CH-SO	Solothurn
\N	1205	4427	CH	CH-SZ	Schwyz
\N	1205	4428	CH	CH-TG	Thurgau
\N	1205	4429	CH	CH-TI	Ticino
\N	1205	4430	CH	CH-UR	Uri
\N	1205	4431	CH	CH-VD	Vaud
\N	1205	4432	CH	CH-VS	Valais
\N	1205	4433	CH	CH-ZG	Zug
\N	1205	4434	CH	CH-ZH	Zurich
\N	1054	4435	CI	CI-06	18 Montagnes
\N	1054	4436	CI	CI-16	Agnebi
\N	1054	4437	CI	CI-09	Bas-Sassandra
\N	1054	4438	CI	CI-10	Denguele
\N	1054	4439	CI	CI-02	Haut-Sassandra
\N	1054	4440	CI	CI-07	Lacs
\N	1054	4441	CI	CI-01	Lagunes
\N	1054	4442	CI	CI-12	Marahoue
\N	1054	4443	CI	CI-05	Moyen-Comoe
\N	1054	4444	CI	CI-11	Nzi-Comoe
\N	1054	4445	CI	CI-03	Savanes
\N	1054	4446	CI	CI-15	Sud-Bandama
\N	1054	4447	CI	CI-13	Sud-Comoe
\N	1054	4448	CI	CI-04	Vallee du Bandama
\N	1054	4449	CI	CI-14	Worodouqou
\N	1054	4450	CI	CI-08	Zanzan
\N	1044	4451	CL	CL-AI	Aisen del General Carlos Ibanez del Campo
\N	1044	4452	CL	CL-AN	Antofagasta
\N	1044	4453	CL	CL-AR	Araucania
\N	1044	4454	CL	CL-AT	Atacama
\N	1044	4455	CL	CL-BI	Bio-Bio
\N	1044	4456	CL	CL-CO	Coquimbo
\N	1044	4457	CL	CL-LI	Libertador General Bernardo O'Higgins
\N	1044	4458	CL	CL-LL	Los Lagos
\N	1044	4459	CL	CL-MA	Magallanes
\N	1044	4460	CL	CL-ML	Maule
\N	1044	4461	CL	CL-RM	Region Metropolitana de Santiago
\N	1044	4462	CL	CL-TA	Tarapaca
\N	1044	4463	CL	CL-VS	Valparaiso
\N	1038	4464	CM	CM-AD	Adamaoua
\N	1038	4465	CM	CM-CE	Centre
\N	1038	4466	CM	CM-ES	East
\N	1038	4467	CM	CM-EN	Far North
\N	1038	4468	CM	CM-NO	North
\N	1038	4469	CM	CM-SW	South
\N	1038	4470	CM	CM-SW	South-West
\N	1038	4471	CM	CM-OU	West
\N	1045	4472	CN	CN-11	Beijing
\N	1045	4473	CN	CN-50	Chongqing
\N	1045	4474	CN	CN-31	Shanghai
\N	1045	4475	CN	CN-12	Tianjin
\N	1045	4476	CN	CN-34	Anhui
\N	1045	4477	CN	CN-35	Fujian
\N	1045	4478	CN	CN-62	Gansu
\N	1045	4479	CN	CN-44	Guangdong
\N	1045	4480	CN	CN-52	Gulzhou
\N	1045	4481	CN	CN-46	Hainan
\N	1045	4482	CN	CN-13	Hebei
\N	1045	4483	CN	CN-23	Heilongjiang
\N	1045	4484	CN	CN-41	Henan
\N	1045	4485	CN	CN-42	Hubei
\N	1045	4486	CN	CN-43	Hunan
\N	1045	4487	CN	CN-32	Jiangsu
\N	1045	4488	CN	CN-36	Jiangxi
\N	1045	4489	CN	CN-22	Jilin
\N	1045	4490	CN	CN-21	Liaoning
\N	1045	4491	CN	CN-63	Qinghai
\N	1045	4492	CN	CN-61	Shaanxi
\N	1045	4493	CN	CN-37	Shandong
\N	1045	4494	CN	CN-14	Shanxi
\N	1045	4495	CN	CN-51	Sichuan
\N	1045	4496	CN	CN-71	Taiwan
\N	1045	4497	CN	CN-53	Yunnan
\N	1045	4498	CN	CN-33	Zhejiang
\N	1045	4499	CN	CN-45	Guangxi
\N	1045	4500	CN	CN-15	Neia Mongol (mn)
\N	1045	4501	CN	CN-65	Xinjiang
\N	1045	4502	CN	CN-54	Xizang
\N	1045	4503	CN	CN-91	Hong Kong
\N	1045	4504	CN	CN-92	Macau
\N	1048	4505	CO	CO-DC	Distrito Capital de Bogotá
\N	1048	4506	CO	CO-AMA	Amazonea
\N	1048	4507	CO	CO-ANT	Antioquia
\N	1048	4508	CO	CO-ARA	Arauca
\N	1048	4509	CO	CO-ATL	Atlántico
\N	1048	4510	CO	CO-BOL	Bolívar
\N	1048	4511	CO	CO-BOY	Boyacá
\N	1048	4512	CO	CO-CAL	Caldea
\N	1048	4513	CO	CO-CAQ	Caquetá
\N	1048	4514	CO	CO-CAS	Casanare
\N	1048	4515	CO	CO-CAU	Cauca
\N	1048	4516	CO	CO-CES	Cesar
\N	1048	4517	CO	CO-COR	Córdoba
\N	1048	4518	CO	CO-CUN	Cundinamarca
\N	1048	4519	CO	CO-CHO	Chocó
\N	1048	4520	CO	CO-GUA	Guainía
\N	1048	4521	CO	CO-GUV	Guaviare
\N	1048	4522	CO	CO-LAG	La Guajira
\N	1048	4523	CO	CO-MAG	Magdalena
\N	1048	4524	CO	CO-MET	Meta
\N	1048	4525	CO	CO-NAR	Nariño
\N	1048	4526	CO	CO-NSA	Norte de Santander
\N	1048	4527	CO	CO-PUT	Putumayo
\N	1048	4528	CO	CO-QUI	Quindio
\N	1048	4529	CO	CO-RIS	Risaralda
\N	1048	4530	CO	CO-SAP	San Andrés, Providencia y Santa Catalina
\N	1048	4531	CO	CO-SAN	Santander
\N	1048	4532	CO	CO-SUC	Sucre
\N	1048	4533	CO	CO-TOL	Tolima
\N	1048	4534	CO	CO-VAC	Valle del Cauca
\N	1048	4535	CO	CO-VAU	Vaupés
\N	1048	4536	CO	CO-VID	Vichada
\N	1053	4537	CR	CR-A	Alajuela
\N	1053	4538	CR	CR-C	Cartago
\N	1053	4539	CR	CR-G	Guanacaste
\N	1053	4540	CR	CR-H	Heredia
\N	1053	4541	CR	CR-L	Limon
\N	1053	4542	CR	CR-P	Puntarenas
\N	1053	4543	CR	CR-SJ	San Jose
\N	1056	4544	CU	CU-09	Camagey
\N	1056	4545	CU	CU-08	Ciego de `vila
\N	1056	4546	CU	CU-06	Cienfuegos
\N	1056	4547	CU	CU-03	Ciudad de La Habana
\N	1056	4548	CU	CU-12	Granma
\N	1056	4549	CU	CU-14	Guantanamo
\N	1056	4550	CU	CU-11	Holquin
\N	1056	4551	CU	CU-02	La Habana
\N	1056	4552	CU	CU-10	Las Tunas
\N	1056	4553	CU	CU-04	Matanzas
\N	1056	4554	CU	CU-01	Pinar del Rio
\N	1056	4555	CU	CU-07	Sancti Spiritus
\N	1056	4556	CU	CU-13	Santiago de Cuba
\N	1056	4557	CU	CU-05	Villa Clara
\N	1056	4558	CU	CU-99	Isla de la Juventud
\N	1056	4559	CU	CU-PR	Pinar del Roo
\N	1056	4560	CU	CU-CA	Ciego de Avila
\N	1056	4561	CU	CU-CG	Camagoey
\N	1056	4562	CU	CU-HO	Holgun
\N	1056	4563	CU	CU-SS	Sancti Spritus
\N	1056	4564	CU	CU-IJ	Municipio Especial Isla de la Juventud
\N	1040	4565	CV	CV-BV	Boa Vista
\N	1040	4566	CV	CV-BR	Brava
\N	1040	4567	CV	CV-CS	Calheta de Sao Miguel
\N	1040	4568	CV	CV-FO	Fogo
\N	1040	4569	CV	CV-MA	Maio
\N	1040	4570	CV	CV-MO	Mosteiros
\N	1040	4571	CV	CV-PA	Paul
\N	1040	4572	CV	CV-PN	Porto Novo
\N	1040	4573	CV	CV-PR	Praia
\N	1040	4574	CV	CV-RG	Ribeira Grande
\N	1040	4575	CV	CV-SL	Sal
\N	1040	4576	CV	CV-SD	Sao Domingos
\N	1040	4577	CV	CV-SF	Sao Filipe
\N	1040	4578	CV	CV-SN	Sao Nicolau
\N	1040	4579	CV	CV-SV	Sao Vicente
\N	1040	4580	CV	CV-TA	Tarrafal
\N	1057	4581	CY	CY-04	Ammochostos Magusa
\N	1057	4582	CY	CY-06	Keryneia
\N	1057	4583	CY	CY-03	Larnaka
\N	1057	4584	CY	CY-01	Lefkosia
\N	1057	4585	CY	CY-02	Lemesos
\N	1057	4586	CY	CY-05	Pafos
\N	1058	4587	CZ	CZ-JC	Jihočeský kraj
\N	1058	4588	CZ	CZ-JM	Jihomoravský kraj
\N	1058	4589	CZ	CZ-KA	Karlovarský kraj
\N	1058	4590	CZ	CZ-KR	Královéhradecký kraj
\N	1058	4591	CZ	CZ-LI	Liberecký kraj
\N	1058	4592	CZ	CZ-MO	Moravskoslezský kraj
\N	1058	4593	CZ	CZ-OL	Olomoucký kraj
\N	1058	4594	CZ	CZ-PA	Pardubický kraj
\N	1058	4595	CZ	CZ-PL	Plzeňský kraj
\N	1058	4596	CZ	CZ-PR	Praha, hlavní město
\N	1058	4597	CZ	CZ-ST	Středočeský kraj
\N	1058	4598	CZ	CZ-US	Ústecký kraj
\N	1058	4599	CZ	CZ-VY	Vysočina
\N	1058	4600	CZ	CZ-ZL	Zlínský kraj
\N	1082	4601	DE	DE-BW	Baden-Wuerttemberg
\N	1082	4602	DE	DE-BY	Bayern
\N	1082	4603	DE	DE-HB	Bremen
\N	1082	4604	DE	DE-HH	Hamburg
\N	1082	4605	DE	DE-HE	Hessen
\N	1082	4606	DE	DE-NI	Niedersachsen
\N	1082	4607	DE	DE-NW	Nordrhein-Westfalen
\N	1082	4608	DE	DE-RP	Rheinland-Pfalz
\N	1082	4609	DE	DE-SL	Saarland
\N	1082	4610	DE	DE-SH	Schleswig-Holstein
\N	1082	4611	DE	DE-BR	Berlin
\N	1082	4612	DE	DE-BB	Brandenburg
\N	1082	4613	DE	DE-MV	Mecklenburg-Vorpommern
\N	1082	4614	DE	DE-SN	Sachsen
\N	1082	4615	DE	DE-ST	Sachsen-Anhalt
\N	1082	4616	DE	DE-TH	Thueringen
\N	1060	4617	DJ	DJ-AS	Ali Sabiah
\N	1060	4618	DJ	DJ-DI	Dikhil
\N	1060	4619	DJ	DJ-DJ	Djibouti
\N	1060	4620	DJ	DJ-OB	Obock
\N	1060	4621	DJ	DJ-TA	Tadjoura
\N	1059	4622	DK	DK-147	Frederikaberg municipality
\N	1059	4623	DK	DK-101	Copenhagen municipality
\N	1059	4624	DK	DK-015	Copenhagen
\N	1059	4625	DK	DK-020	Frederiksborg
\N	1059	4626	DK	DK-025	Roskilde
\N	1059	4627	DK	DK-030	Western Zealand
\N	1059	4628	DK	DK-035	Storstrøm
\N	1059	4629	DK	DK-040	Bornholm
\N	1059	4630	DK	DK-042	Funen
\N	1059	4631	DK	DK-050	Southern Jutland
\N	1059	4632	DK	DK-055	Ribe
\N	1059	4633	DK	DK-060	Vejle
\N	1059	4634	DK	DK-065	Ringkøbing
\N	1059	4635	DK	DK-070	Aarhus
\N	1059	4636	DK	DK-076	Viborg
\N	1059	4637	DK	DK-080	Northern Jutland
\N	1062	4638	DO	DO-01	Distrito Nacional (Santo Domingo)
\N	1062	4639	DO	DO-02	Azua
\N	1062	4640	DO	DO-03	Bahoruco
\N	1062	4641	DO	DO-04	Barahona
\N	1062	4642	DO	DO-05	Dajabón
\N	1062	4643	DO	DO-06	Duarte
\N	1062	4644	DO	DO-08	El Seybo [El Seibo]
\N	1062	4645	DO	DO-09	Espaillat
\N	1062	4646	DO	DO-30	Hato Mayor
\N	1062	4647	DO	DO-10	Independencia
\N	1062	4648	DO	DO-11	La Altagracia
\N	1062	4649	DO	DO-07	La Estrelleta [Elias Pina]
\N	1062	4650	DO	DO-12	La Romana
\N	1062	4651	DO	DO-13	La Vega
\N	1062	4652	DO	DO-14	Maroia Trinidad Sánchez
\N	1062	4653	DO	DO-28	Monseñor Nouel
\N	1062	4654	DO	DO-15	Monte Cristi
\N	1062	4655	DO	DO-29	Monte Plata
\N	1062	4656	DO	DO-16	Pedernales
\N	1062	4657	DO	DO-17	Peravia
\N	1062	4658	DO	DO-18	Puerto Plata
\N	1062	4659	DO	DO-19	Salcedo
\N	1062	4660	DO	DO-20	Samaná
\N	1062	4661	DO	DO-21	San Cristóbal
\N	1062	4662	DO	DO-23	San Pedro de Macorís
\N	1062	4663	DO	DO-24	Sánchez Ramírez
\N	1062	4664	DO	DO-25	Santiago
\N	1062	4665	DO	DO-26	Santiago Rodríguez
\N	1062	4666	DO	DO-27	Valverde
\N	1003	4667	DZ	DZ-01	Adrar
\N	1003	4668	DZ	DZ-44	Ain Defla
\N	1003	4669	DZ	DZ-46	Ain Tmouchent
\N	1003	4670	DZ	DZ-16	Alger
\N	1003	4671	DZ	DZ-23	Annaba
\N	1003	4672	DZ	DZ-05	Batna
\N	1003	4673	DZ	DZ-08	Bechar
\N	1003	4674	DZ	DZ-06	Bejaia
\N	1003	4675	DZ	DZ-07	Biskra
\N	1003	4676	DZ	DZ-09	Blida
\N	1003	4677	DZ	DZ-34	Bordj Bou Arreridj
\N	1003	4678	DZ	DZ-10	Bouira
\N	1003	4679	DZ	DZ-35	Boumerdes
\N	1003	4680	DZ	DZ-02	Chlef
\N	1003	4681	DZ	DZ-25	Constantine
\N	1003	4682	DZ	DZ-17	Djelfa
\N	1003	4683	DZ	DZ-32	El Bayadh
\N	1003	4684	DZ	DZ-39	El Oued
\N	1003	4685	DZ	DZ-36	El Tarf
\N	1003	4686	DZ	DZ-47	Ghardaia
\N	1003	4687	DZ	DZ-24	Guelma
\N	1003	4688	DZ	DZ-33	Illizi
\N	1003	4689	DZ	DZ-18	Jijel
\N	1003	4690	DZ	DZ-40	Khenchela
\N	1003	4691	DZ	DZ-03	Laghouat
\N	1003	4692	DZ	DZ-29	Mascara
\N	1003	4693	DZ	DZ-26	Medea
\N	1003	4694	DZ	DZ-43	Mila
\N	1003	4695	DZ	DZ-27	Mostaganem
\N	1003	4696	DZ	DZ-28	Msila
\N	1003	4697	DZ	DZ-45	Naama
\N	1003	4698	DZ	DZ-31	Oran
\N	1003	4699	DZ	DZ-30	Ouargla
\N	1003	4700	DZ	DZ-04	Oum el Bouaghi
\N	1003	4701	DZ	DZ-48	Relizane
\N	1003	4702	DZ	DZ-20	Saida
\N	1003	4703	DZ	DZ-19	Setif
\N	1003	4704	DZ	DZ-22	Sidi Bel Abbes
\N	1003	4705	DZ	DZ-21	Skikda
\N	1003	4706	DZ	DZ-41	Souk Ahras
\N	1003	4707	DZ	DZ-11	Tamanghasset
\N	1003	4708	DZ	DZ-12	Tebessa
\N	1003	4709	DZ	DZ-14	Tiaret
\N	1003	4710	DZ	DZ-37	Tindouf
\N	1003	4711	DZ	DZ-42	Tipaza
\N	1003	4712	DZ	DZ-38	Tissemsilt
\N	1003	4713	DZ	DZ-15	Tizi Ouzou
\N	1003	4714	DZ	DZ-13	Tlemcen
\N	1064	4715	EC	EC-A	Azuay
\N	1064	4716	EC	EC-B	Bolivar
\N	1064	4717	EC	EC-F	Canar
\N	1064	4718	EC	EC-C	Carchi
\N	1064	4719	EC	EC-X	Cotopaxi
\N	1064	4720	EC	EC-H	Chimborazo
\N	1064	4721	EC	EC-O	El Oro
\N	1064	4722	EC	EC-E	Esmeraldas
\N	1064	4723	EC	EC-W	Galapagos
\N	1064	4724	EC	EC-G	Guayas
\N	1064	4725	EC	EC-I	Imbabura
\N	1064	4726	EC	EC-L	Loja
\N	1064	4727	EC	EC-R	Los Rios
\N	1064	4728	EC	EC-M	Manabi
\N	1064	4729	EC	EC-S	Morona-Santiago
\N	1064	4730	EC	EC-N	Napo
\N	1064	4731	EC	EC-D	Orellana
\N	1064	4732	EC	EC-Y	Pastaza
\N	1064	4733	EC	EC-P	Pichincha
\N	1064	4734	EC	EC-U	Sucumbios
\N	1064	4735	EC	EC-T	Tungurahua
\N	1064	4736	EC	EC-Z	Zamora-Chinchipe
\N	1069	4737	EE	EE-37	Harjumsa
\N	1069	4738	EE	EE-39	Hitumea
\N	1069	4739	EE	EE-44	Ida-Virumsa
\N	1069	4740	EE	EE-49	Jogevamsa
\N	1069	4741	EE	EE-51	Jarvamsa
\N	1069	4742	EE	EE-57	Lasnemsa
\N	1069	4743	EE	EE-59	Laane-Virumaa
\N	1069	4744	EE	EE-65	Polvamea
\N	1069	4745	EE	EE-67	Parnumsa
\N	1069	4746	EE	EE-70	Raplamsa
\N	1069	4747	EE	EE-74	Saaremsa
\N	1069	4748	EE	EE-7B	Tartumsa
\N	1069	4749	EE	EE-82	Valgamaa
\N	1069	4750	EE	EE-84	Viljandimsa
\N	1069	4751	EE	EE-86	Vorumaa
\N	1065	4752	EG	EG-DK	Ad Daqahllyah
\N	1065	4753	EG	EG-BA	Al Bahr al Ahmar
\N	1065	4754	EG	EG-BH	Al Buhayrah
\N	1065	4755	EG	EG-FYM	Al Fayym
\N	1065	4756	EG	EG-GH	Al Gharbiyah
\N	1065	4757	EG	EG-ALX	Al Iskandarlyah
\N	1065	4758	EG	EG-IS	Al Isma illyah
\N	1065	4759	EG	EG-GZ	Al Jizah
\N	1065	4760	EG	EG-MNF	Al Minuflyah
\N	1065	4761	EG	EG-MN	Al Minya
\N	1065	4762	EG	EG-C	Al Qahirah
\N	1065	4763	EG	EG-KB	Al Qalyublyah
\N	1065	4764	EG	EG-WAD	Al Wadi al Jadid
\N	1065	4765	EG	EG-SHR	Ash Sharqiyah
\N	1065	4766	EG	EG-SUZ	As Suways
\N	1065	4767	EG	EG-ASN	Aswan
\N	1065	4768	EG	EG-AST	Asyut
\N	1065	4769	EG	EG-BNS	Bani Suwayf
\N	1065	4770	EG	EG-PTS	Bur Sa'id
\N	1065	4771	EG	EG-DT	Dumyat
\N	1065	4772	EG	EG-JS	Janub Sina'
\N	1065	4773	EG	EG-KFS	Kafr ash Shaykh
\N	1065	4774	EG	EG-MT	Matruh
\N	1065	4775	EG	EG-KN	Qina
\N	1065	4776	EG	EG-SIN	Shamal Sina'
\N	1065	4777	EG	EG-SHG	Suhaj
\N	1068	4778	ER	ER-AN	Anseba
\N	1068	4779	ER	ER-DU	Debub
\N	1068	4780	ER	ER-DK	Debubawi Keyih Bahri [Debub-Keih-Bahri]
\N	1068	4781	ER	ER-GB	Gash-Barka
\N	1068	4782	ER	ER-MA	Maakel [Maekel]
\N	1068	4783	ER	ER-SK	Semenawi Keyih Bahri [Semien-Keih-Bahri]
\N	1198	4784	ES	ES-VI	Álava
\N	1198	4785	ES	ES-AB	Albacete
\N	1198	4786	ES	ES-A	Alicante
\N	1198	4787	ES	ES-AL	Almería
\N	1198	4788	ES	ES-O	Asturias
\N	1198	4789	ES	ES-AV	Ávila
\N	1198	4790	ES	ES-BA	Badajoz
\N	1198	4791	ES	ES-PM	Baleares
\N	1198	4792	ES	ES-B	Barcelona
\N	1198	4793	ES	ES-BU	Burgos
\N	1198	4794	ES	ES-CC	Cáceres
\N	1198	4795	ES	ES-CA	Cádiz
\N	1198	4796	ES	ES-S	Cantabria
\N	1198	4797	ES	ES-CS	Castellón
\N	1198	4798	ES	ES-CR	Ciudad Real
\N	1198	4799	ES	ES-CU	Cuenca
\N	1198	4800	ES	ES-GE	Girona [Gerona]
\N	1198	4801	ES	ES-GR	Granada
\N	1198	4802	ES	ES-GU	Guadalajara
\N	1198	4803	ES	ES-SS	Guipúzcoa
\N	1198	4804	ES	ES-H	Huelva
\N	1198	4805	ES	ES-HU	Huesca
\N	1198	4806	ES	ES-J	Jaén
\N	1198	4807	ES	ES-C	La Coruña
\N	1198	4808	ES	ES-LO	La Rioja
\N	1198	4809	ES	ES-GC	Las Palmas
\N	1198	4810	ES	ES-LE	León
\N	1198	4811	ES	ES-L	Lleida [Lérida]
\N	1198	4812	ES	ES-LU	Lugo
\N	1198	4813	ES	ES-M	Madrid
\N	1198	4814	ES	ES-MA	Málaga
\N	1198	4815	ES	ES-MU	Murcia
\N	1198	4816	ES	ES-NA	Navarra
\N	1198	4817	ES	ES-OR	Ourense
\N	1198	4818	ES	ES-P	Palencia
\N	1198	4819	ES	ES-PO	Pontevedra
\N	1198	4820	ES	ES-SA	Salamanca
\N	1198	4821	ES	ES-TF	Santa Cruz de Tenerife
\N	1198	4822	ES	ES-SG	Segovia
\N	1198	4823	ES	ES-SE	Sevilla
\N	1198	4824	ES	ES-SO	Soria
\N	1198	4825	ES	ES-T	Tarragona
\N	1198	4826	ES	ES-TE	Teruel
\N	1198	4827	ES	ES-V	Valencia
\N	1198	4828	ES	ES-VA	Valladolid
\N	1198	4829	ES	ES-BI	Vizcaya
\N	1198	4830	ES	ES-ZA	Zamora
\N	1198	4831	ES	ES-Z	Zaragoza
\N	1198	4832	ES	ES-CE	Ceuta
\N	1198	4833	ES	ES-ML	Melilla
\N	1070	4834	ET	ET-AA	Addis Ababa
\N	1070	4835	ET	ET-DD	Dire Dawa
\N	1070	4836	ET	ET-AF	Afar
\N	1070	4837	ET	ET-AM	Amara
\N	1070	4838	ET	ET-BE	Benshangul-Gumaz
\N	1070	4839	ET	ET-GA	Gambela Peoples
\N	1070	4840	ET	ET-HA	Harari People
\N	1070	4841	ET	ET-OR	Oromia
\N	1070	4842	ET	ET-SO	Somali
\N	1070	4843	ET	ET-SN	Southern Nations, Nationalities and Peoples
\N	1070	4844	ET	ET-TI	Tigrai
\N	1075	4845	FI	FI-AL	Ahvenanmasn laani
\N	1075	4846	FI	FI-ES	Etela-Suomen laani
\N	1075	4847	FI	FI-IS	Ita-Suomen lasni
\N	1075	4848	FI	FI-LL	Lapin Laani
\N	1075	4849	FI	FI-LS	Lansi-Suomen Laani
\N	1075	4850	FI	FI-OL	Oulun Lasni
\N	1074	4851	FJ	FJ-E	Eastern
\N	1074	4852	FJ	FJ-N	Northern
\N	1074	4853	FJ	FJ-W	Western
\N	1074	4854	FJ	FJ-R	Rotuma
\N	1141	4855	FM	FM-TRK	Chuuk
\N	1141	4856	FM	FM-KSA	Kosrae
\N	1141	4857	FM	FM-PNI	Pohnpei
\N	1141	4858	FM	FM-YAP	Yap
\N	1076	4859	FR	FR-01	Ain
\N	1076	4860	FR	FR-02	Aisne
\N	1076	4861	FR	FR-03	Allier
\N	1076	4862	FR	FR-04	Alpes-de-Haute-Provence
\N	1076	4863	FR	FR-06	Alpes-Maritimes
\N	1076	4864	FR	FR-07	Ardèche
\N	1076	4865	FR	FR-08	Ardennes
\N	1076	4866	FR	FR-09	Ariège
\N	1076	4867	FR	FR-10	Aube
\N	1076	4868	FR	FR-11	Aude
\N	1076	4869	FR	FR-12	Aveyron
\N	1076	4870	FR	FR-67	Bas-Rhin
\N	1076	4871	FR	FR-13	Bouches-du-Rhône
\N	1076	4872	FR	FR-14	Calvados
\N	1076	4873	FR	FR-15	Cantal
\N	1076	4874	FR	FR-16	Charente
\N	1076	4875	FR	FR-17	Charente-Maritime
\N	1076	4876	FR	FR-18	Cher
\N	1076	4877	FR	FR-19	Corrèze
\N	1076	4878	FR	FR-20A	Corse-du-Sud
\N	1076	4879	FR	FR-21	Côte-d'Or
\N	1076	4880	FR	FR-22	Côtes-d'Armor
\N	1076	4881	FR	FR-23	Creuse
\N	1076	4882	FR	FR-79	Deux-Sèvres
\N	1076	4883	FR	FR-24	Dordogne
\N	1076	4884	FR	FR-25	Doubs
\N	1076	4885	FR	FR-26	Drôme
\N	1076	4886	FR	FR-91	Essonne
\N	1076	4887	FR	FR-27	Eure
\N	1076	4888	FR	FR-28	Eure-et-Loir
\N	1076	4889	FR	FR-29	Finistère
\N	1076	4890	FR	FR-30	Gard
\N	1076	4891	FR	FR-32	Gers
\N	1076	4892	FR	FR-33	Gironde
\N	1076	4893	FR	FR-68	Haut-Rhin
\N	1076	4894	FR	FR-20B	Haute-Corse
\N	1076	4895	FR	FR-31	Haute-Garonne
\N	1076	4896	FR	FR-43	Haute-Loire
\N	1076	4897	FR	FR-70	Haute-Saône
\N	1076	4898	FR	FR-74	Haute-Savoie
\N	1076	4899	FR	FR-87	Haute-Vienne
\N	1076	4900	FR	FR-05	Hautes-Alpes
\N	1076	4901	FR	FR-65	Hautes-Pyrénées
\N	1076	4902	FR	FR-92	Hauts-de-Seine
\N	1076	4903	FR	FR-34	Hérault
\N	1076	4904	FR	FR-35	Indre
\N	1076	4905	FR	FR-36	Ille-et-Vilaine
\N	1076	4906	FR	FR-37	Indre-et-Loire
\N	1076	4907	FR	FR-38	Isère
\N	1076	4908	FR	FR-40	Landes
\N	1076	4909	FR	FR-41	Loir-et-Cher
\N	1076	4910	FR	FR-42	Loire
\N	1076	4911	FR	FR-44	Loire-Atlantique
\N	1076	4912	FR	FR-45	Loiret
\N	1076	4913	FR	FR-46	Lot
\N	1076	4914	FR	FR-47	Lot-et-Garonne
\N	1076	4915	FR	FR-48	Lozère
\N	1076	4916	FR	FR-49	Maine-et-Loire
\N	1076	4917	FR	FR-50	Manche
\N	1076	4918	FR	FR-51	Marne
\N	1076	4919	FR	FR-53	Mayenne
\N	1076	4920	FR	FR-54	Meurthe-et-Moselle
\N	1076	4921	FR	FR-55	Meuse
\N	1076	4922	FR	FR-56	Morbihan
\N	1076	4923	FR	FR-57	Moselle
\N	1076	4924	FR	FR-58	Nièvre
\N	1076	4925	FR	FR-59	Nord
\N	1076	4926	FR	FR-60	Oise
\N	1076	4927	FR	FR-61	Orne
\N	1076	4928	FR	FR-75	Paris
\N	1076	4929	FR	FR-62	Pas-de-Calais
\N	1076	4930	FR	FR-63	Puy-de-Dôme
\N	1076	4931	FR	FR-64	Pyrénées-Atlantiques
\N	1076	4932	FR	FR-66	Pyrénées-Orientales
\N	1076	4933	FR	FR-69	Rhône
\N	1076	4934	FR	FR-71	Saône-et-Loire
\N	1076	4935	FR	FR-72	Sarthe
\N	1076	4936	FR	FR-73	Savoie
\N	1076	4937	FR	FR-77	Seine-et-Marne
\N	1076	4938	FR	FR-76	Seine-Maritime
\N	1076	4939	FR	FR-93	Seine-Saint-Denis
\N	1076	4940	FR	FR-80	Somme
\N	1076	4941	FR	FR-81	Tarn
\N	1076	4942	FR	FR-82	Tarn-et-Garonne
\N	1076	4943	FR	FR-95	Val d'Oise
\N	1076	4944	FR	FR-90	Territoire de Belfort
\N	1076	4945	FR	FR-94	Val-de-Marne
\N	1076	4946	FR	FR-83	Var
\N	1076	4947	FR	FR-84	Vaucluse
\N	1076	4948	FR	FR-85	Vendée
\N	1076	4949	FR	FR-86	Vienne
\N	1076	4950	FR	FR-88	Vosges
\N	1076	4951	FR	FR-89	Yonne
\N	1076	4952	FR	FR-78	Yvelines
\N	1226	4953	GB	GB-ABE	Aberdeen City
\N	1226	4954	GB	GB-ABD	Aberdeenshire
\N	1226	4955	GB	GB-ANS	Angus
\N	1226	4956	GB	GB-ANT	Antrim
\N	1226	4957	GB	GB-ARD	Ards
\N	1226	4958	GB	GB-AGB	Argyll and Bute
\N	1226	4959	GB	GB-ARM	Armagh
\N	1226	4960	GB	GB-BLA	Ballymena
\N	1226	4961	GB	GB-BLY	Ballymoney
\N	1226	4962	GB	GB-BNB	Banbridge
\N	1226	4963	GB	GB-BDG	Barking and Dagenham
\N	1226	4964	GB	GB-BNE	Barnet
\N	1226	4965	GB	GB-BNS	Barnsley
\N	1226	4966	GB	GB-BAS	Bath and North East Somerset
\N	1226	4967	GB	GB-BDF	Bedfordshire
\N	1226	4968	GB	GB-BFS	Belfast
\N	1226	4969	GB	GB-BEX	Bexley
\N	1226	4970	GB	GB-BIR	Birmingham
\N	1226	4971	GB	GB-BBD	Blackburn with Darwen
\N	1226	4972	GB	GB-BPL	Blackpool
\N	1226	4973	GB	GB-BGW	Blaenau Gwent
\N	1226	4974	GB	GB-BOL	Bolton
\N	1226	4975	GB	GB-BMH	Bournemouth
\N	1226	4976	GB	GB-BRC	Bracknell Forest
\N	1226	4977	GB	GB-BRD	Bradford
\N	1226	4978	GB	GB-BEN	Brent
\N	1226	4979	GB	GB-BGE	Bridgend
\N	1226	4980	GB	GB-BNH	Brighton and Hove
\N	1226	4981	GB	GB-BST	Bristol, City of
\N	1226	4982	GB	GB-BRY	Bromley
\N	1226	4983	GB	GB-BKM	Buckinghamshire
\N	1226	4984	GB	GB-BUR	Bury
\N	1226	4985	GB	GB-CAY	Caerphilly
\N	1226	4986	GB	GB-CLD	Calderdale
\N	1226	4987	GB	GB-CAM	Cambridgeshire
\N	1226	4988	GB	GB-CMD	Camden
\N	1226	4989	GB	GB-CRF	Cardiff
\N	1226	4990	GB	GB-CMN	Carmarthenshire
\N	1226	4991	GB	GB-GFY	Sir Gaerfyrddin
\N	1226	4992	GB	GB-CKF	Carrickfergus
\N	1226	4993	GB	GB-CSR	Castlereagh
\N	1226	4994	GB	GB-CGN	Ceredigion
\N	1226	4995	GB	GB-CHS	Cheshire
\N	1226	4996	GB	GB-CLK	Clackmannanshire
\N	1226	4997	GB	GB-CLR	Coleraine
\N	1226	4998	GB	GB-CWY	Conwy
\N	1226	4999	GB	GB-CKT	Cookstown
\N	1226	5000	GB	GB-CON	Cornwall
\N	1226	5001	GB	GB-COV	Coventry
\N	1226	5002	GB	GB-CGV	Cralgavon
\N	1226	5003	GB	GB-CRY	Croydon
\N	1226	5004	GB	GB-CMA	Cumbria
\N	1226	5005	GB	GB-DAL	Darlington
\N	1226	5006	GB	GB-DEN	Denbighshire
\N	1226	5007	GB	GB-DER	Derby
\N	1226	5008	GB	GB-DBY	Derbyshire
\N	1226	5009	GB	GB-DRY	Derry
\N	1226	5010	GB	GB-DEV	Devon
\N	1226	5011	GB	GB-DNC	Doncaster
\N	1226	5012	GB	GB-DOR	Dorset
\N	1226	5013	GB	GB-DOW	Down
\N	1226	5014	GB	GB-DUD	Dudley
\N	1226	5015	GB	GB-DGY	Dumfries and Galloway
\N	1226	5016	GB	GB-DND	Dundee City
\N	1226	5017	GB	GB-DGN	Dungannon
\N	1226	5018	GB	GB-DUR	Durham
\N	1226	5019	GB	GB-EAL	Ealing
\N	1226	5020	GB	GB-EAY	East Ayrshire
\N	1226	5021	GB	GB-EDU	East Dunbartonshire
\N	1226	5022	GB	GB-ELN	East Lothian
\N	1226	5023	GB	GB-ERW	East Renfrewshire
\N	1226	5024	GB	GB-ERY	East Riding of Yorkshire
\N	1226	5025	GB	GB-ESX	East Sussex
\N	1226	5026	GB	GB-EDH	Edinburgh, City of
\N	1226	5027	GB	GB-ELS	Eilean Siar
\N	1226	5028	GB	GB-ENF	Enfield
\N	1226	5029	GB	GB-ESS	Essex
\N	1226	5030	GB	GB-FAL	Falkirk
\N	1226	5031	GB	GB-FER	Fermanagh
\N	1226	5032	GB	GB-FIF	Fife
\N	1226	5033	GB	GB-FLN	Flintshire
\N	1226	5034	GB	GB-GAT	Gateshead
\N	1226	5035	GB	GB-GLG	Glasgow City
\N	1226	5036	GB	GB-GLS	Gloucestershire
\N	1226	5037	GB	GB-GRE	Greenwich
\N	1226	5038	GB	GB-GSY	Guernsey
\N	1226	5039	GB	GB-GWN	Gwynedd
\N	1226	5040	GB	GB-HCK	Hackney
\N	1226	5041	GB	GB-HAL	Halton
\N	1226	5042	GB	GB-HMF	Hammersmith and Fulham
\N	1226	5043	GB	GB-HAM	Hampshire
\N	1226	5044	GB	GB-HRY	Haringey
\N	1226	5045	GB	GB-HRW	Harrow
\N	1226	5046	GB	GB-HPL	Hartlepool
\N	1226	5047	GB	GB-HAV	Havering
\N	1226	5048	GB	GB-HEF	Herefordshire, County of
\N	1226	5049	GB	GB-HRT	Hertfordshire
\N	1226	5050	GB	GB-HED	Highland
\N	1226	5051	GB	GB-HIL	Hillingdon
\N	1226	5052	GB	GB-HNS	Hounslow
\N	1226	5053	GB	GB-IVC	Inverclyde
\N	1226	5054	GB	GB-AGY	Isle of Anglesey
\N	1226	5055	GB	GB-IOW	Isle of Wight
\N	1226	5056	GB	GB-IOS	Isles of Scilly
\N	1226	5057	GB	GB-ISL	Islington
\N	1226	5058	GB	GB-JSY	Jersey
\N	1226	5059	GB	GB-KEC	Kensington and Chelsea
\N	1226	5060	GB	GB-KEN	Kent
\N	1226	5061	GB	GB-KHL	Kingston upon Hull, City of
\N	1226	5062	GB	GB-KTT	Kingston upon Thames
\N	1226	5063	GB	GB-KIR	Kirklees
\N	1226	5064	GB	GB-KWL	Knowsley
\N	1226	5065	GB	GB-LBH	Lambeth
\N	1226	5066	GB	GB-LAN	Lancashire
\N	1226	5067	GB	GB-LRN	Larne
\N	1226	5068	GB	GB-LDS	Leeds
\N	1226	5069	GB	GB-LCE	Leicester
\N	1226	5070	GB	GB-LEC	Leicestershire
\N	1226	5071	GB	GB-LEW	Lewisham
\N	1226	5072	GB	GB-LMV	Limavady
\N	1226	5073	GB	GB-LIN	Lincolnshire
\N	1226	5074	GB	GB-LSB	Lisburn
\N	1226	5075	GB	GB-LIV	Liverpool
\N	1226	5076	GB	GB-LND	London, City of
\N	1226	5077	GB	GB-LUT	Luton
\N	1226	5078	GB	GB-MFT	Magherafelt
\N	1226	5079	GB	GB-MAN	Manchester
\N	1226	5080	GB	GB-MDW	Medway
\N	1226	5081	GB	GB-MTY	Merthyr Tydfil
\N	1226	5082	GB	GB-MRT	Merton
\N	1226	5083	GB	GB-MDB	Middlesbrough
\N	1226	5084	GB	GB-MLN	Midlothian
\N	1226	5085	GB	GB-MIK	Milton Keynes
\N	1226	5086	GB	GB-MON	Monmouthshire
\N	1226	5087	GB	GB-MRY	Moray
\N	1226	5088	GB	GB-MYL	Moyle
\N	1226	5089	GB	GB-NTL	Neath Port Talbot
\N	1226	5090	GB	GB-NET	Newcastle upon Tyne
\N	1226	5091	GB	GB-NWM	Newham
\N	1226	5092	GB	GB-NWP	Newport
\N	1226	5093	GB	GB-NYM	Newry and Mourne
\N	1226	5094	GB	GB-NTA	Newtownabbey
\N	1226	5095	GB	GB-NFK	Norfolk
\N	1226	5096	GB	GB-NAY	North Ayrahire
\N	1226	5097	GB	GB-NDN	North Down
\N	1226	5098	GB	GB-NEL	North East Lincolnshire
\N	1226	5099	GB	GB-NLK	North Lanarkshire
\N	1226	5100	GB	GB-NLN	North Lincolnshire
\N	1226	5101	GB	GB-NSM	North Somerset
\N	1226	5102	GB	GB-NTY	North Tyneside
\N	1226	5103	GB	GB-NYK	North Yorkshire
\N	1226	5104	GB	GB-NTH	Northamptonshire
\N	1226	5105	GB	GB-NBL	Northumbarland
\N	1226	5106	GB	GB-NGM	Nottingham
\N	1226	5107	GB	GB-NTT	Nottinghamshire
\N	1226	5108	GB	GB-OLD	Oldham
\N	1226	5109	GB	GB-OMH	Omagh
\N	1226	5110	GB	GB-ORR	Orkney Islands
\N	1226	5111	GB	GB-OXF	Oxfordshire
\N	1226	5112	GB	GB-PEM	Pembrokeshire
\N	1226	5113	GB	GB-PKN	Perth and Kinross
\N	1226	5114	GB	GB-PTE	Peterborough
\N	1226	5115	GB	GB-PLY	Plymouth
\N	1226	5116	GB	GB-POL	Poole
\N	1226	5117	GB	GB-POR	Portsmouth
\N	1226	5118	GB	GB-POW	Powys
\N	1226	5119	GB	GB-RDG	Reading
\N	1226	5120	GB	GB-RDB	Redbridge
\N	1226	5121	GB	GB-RCC	Redcar and Cleveland
\N	1226	5122	GB	GB-RFW	Renfrewshlre
\N	1226	5123	GB	GB-RCT	Rhondda, Cynon, Taff
\N	1226	5124	GB	GB-RIC	Richmond upon Thames
\N	1226	5125	GB	GB-RCH	Rochdale
\N	1226	5126	GB	GB-ROT	Rotherham
\N	1226	5127	GB	GB-RUT	Rutland
\N	1226	5128	GB	GB-SHN	St. Helens
\N	1226	5129	GB	GB-SLF	Salford
\N	1226	5130	GB	GB-SAW	Sandwell
\N	1226	5131	GB	GB-SCB	Scottish Borders, The
\N	1226	5132	GB	GB-SFT	Sefton
\N	1226	5133	GB	GB-SHF	Sheffield
\N	1226	5134	GB	GB-ZET	Shetland Islands
\N	1226	5135	GB	GB-SHR	Shropshire
\N	1226	5136	GB	GB-SLG	Slough
\N	1226	5137	GB	GB-SOL	Solihull
\N	1226	5138	GB	GB-SOM	merset
\N	1226	5139	GB	GB-SAY	South Ayrshire
\N	1226	5140	GB	GB-SGC	South Gloucestershire
\N	1226	5141	GB	GB-SLK	South Lanarkshire
\N	1226	5142	GB	GB-STY	South Tyneside
\N	1226	5143	GB	GB-STH	Southampton
\N	1226	5144	GB	GB-SOS	Southend-on-Sea
\N	1226	5145	GB	GB-SWK	Southwark
\N	1226	5146	GB	GB-STS	Staffordshire
\N	1226	5147	GB	GB-STG	Stirling
\N	1226	5148	GB	GB-SKP	Stockport
\N	1226	5149	GB	GB-STT	Stockton-on-Tees
\N	1226	5150	GB	GB-STE	Stoke-on-Trent
\N	1226	5151	GB	GB-STB	Strabane
\N	1226	5152	GB	GB-SFK	Suffolk
\N	1226	5153	GB	GB-SND	Sunderland
\N	1226	5154	GB	GB-SRY	Surrey
\N	1226	5155	GB	GB-STN	Sutton
\N	1226	5156	GB	GB-SWA	Swansea
\N	1226	5157	GB	GB-SWD	Swindon
\N	1226	5158	GB	GB-TAM	Tameside
\N	1226	5159	GB	GB-TFW	Telford and Wrekin
\N	1226	5160	GB	GB-THR	Thurrock
\N	1226	5161	GB	GB-TOB	Torbay
\N	1226	5162	GB	GB-TOF	Torfasn
\N	1226	5163	GB	GB-TWH	Tower Hamlets
\N	1226	5164	GB	GB-TRF	Trafford
\N	1226	5165	GB	GB-VGL	Vale of Glamorgan, The
\N	1226	5166	GB	GB-BMG	Bro Morgannwg
\N	1226	5167	GB	GB-WKF	Wakefield
\N	1226	5168	GB	GB-WLL	Walsall
\N	1226	5169	GB	GB-WFT	Waltham Forest
\N	1226	5170	GB	GB-WND	Wandsworth
\N	1226	5171	GB	GB-WRT	Warrington
\N	1226	5172	GB	GB-WAR	Warwickshire
\N	1226	5173	GB	GB-WBX	West Berkshire
\N	1226	5174	GB	GB-WDU	West Dunbartonshire
\N	1226	5175	GB	GB-WLN	West Lothian
\N	1226	5176	GB	GB-WSX	West Sussex
\N	1226	5177	GB	GB-WSM	Westminster
\N	1226	5178	GB	GB-WGN	Wigan
\N	1226	5179	GB	GB-WIL	Wiltshire
\N	1226	5180	GB	GB-WNM	Windsor and Maidenhead
\N	1226	5181	GB	GB-WRL	Wirral
\N	1226	5182	GB	GB-WOK	Wokingham
\N	1226	5183	GB	GB-WLV	Wolverhampton
\N	1226	5184	GB	GB-WOR	Worcestershire
\N	1226	5185	GB	GB-WRX	Wrexham
\N	1226	5186	GB	GB-YOR	York
\N	1083	5187	GH	GH-AH	Ashanti
\N	1083	5188	GH	GH-BA	Brong-Ahafo
\N	1083	5189	GH	GH-AA	Greater Accra
\N	1083	5190	GH	GH-UE	Upper East
\N	1083	5191	GH	GH-UW	Upper West
\N	1083	5192	GH	GH-TV	Volta
\N	1213	5193	GM	GM-B	Banjul
\N	1213	5194	GM	GM-L	Lower River
\N	1213	5195	GM	GM-M	MacCarthy Island
\N	1213	5196	GM	GM-N	North Bank
\N	1213	5197	GM	GM-U	Upper River
\N	1091	5198	GN	GN-BE	Beyla
\N	1091	5199	GN	GN-BF	Boffa
\N	1091	5200	GN	GN-BK	Boke
\N	1091	5201	GN	GN-CO	Coyah
\N	1091	5202	GN	GN-DB	Dabola
\N	1091	5203	GN	GN-DL	Dalaba
\N	1091	5204	GN	GN-DI	Dinguiraye
\N	1091	5205	GN	GN-DU	Dubreka
\N	1091	5206	GN	GN-FA	Faranah
\N	1091	5207	GN	GN-FO	Forecariah
\N	1091	5208	GN	GN-FR	Fria
\N	1091	5209	GN	GN-GA	Gaoual
\N	1091	5210	GN	GN-GU	Guekedou
\N	1091	5211	GN	GN-KA	Kankan
\N	1091	5212	GN	GN-KE	Kerouane
\N	1091	5213	GN	GN-KD	Kindia
\N	1091	5214	GN	GN-KS	Kissidougou
\N	1091	5215	GN	GN-KB	Koubia
\N	1091	5216	GN	GN-KN	Koundara
\N	1091	5217	GN	GN-KO	Kouroussa
\N	1091	5218	GN	GN-LA	Labe
\N	1091	5219	GN	GN-LE	Lelouma
\N	1091	5220	GN	GN-LO	Lola
\N	1091	5221	GN	GN-MC	Macenta
\N	1091	5222	GN	GN-ML	Mali
\N	1091	5223	GN	GN-MM	Mamou
\N	1091	5224	GN	GN-MD	Mandiana
\N	1091	5225	GN	GN-NZ	Nzerekore
\N	1091	5226	GN	GN-PI	Pita
\N	1091	5227	GN	GN-SI	Siguiri
\N	1091	5228	GN	GN-TE	Telimele
\N	1091	5229	GN	GN-TO	Tougue
\N	1091	5230	GN	GN-YO	Yomou
\N	1067	5231	GQ	GQ-C	Region Continental
\N	1067	5232	GQ	GQ-I	Region Insular
\N	1067	5233	GQ	GQ-AN	Annobon
\N	1067	5234	GQ	GQ-BN	Bioko Norte
\N	1067	5235	GQ	GQ-BS	Bioko Sur
\N	1067	5236	GQ	GQ-CS	Centro Sur
\N	1067	5237	GQ	GQ-KN	Kie-Ntem
\N	1067	5238	GQ	GQ-LI	Litoral
\N	1067	5239	GQ	GQ-WN	Wele-Nzas
\N	1085	5240	GR	GR-13	Achaa
\N	1085	5241	GR	GR-01	Aitolia-Akarnania
\N	1085	5242	GR	GR-11	Argolis
\N	1085	5243	GR	GR-12	Arkadia
\N	1085	5244	GR	GR-31	Arta
\N	1085	5245	GR	GR-A1	Attiki
\N	1085	5246	GR	GR-64	Chalkidiki
\N	1085	5247	GR	GR-94	Chania
\N	1085	5248	GR	GR-85	Chios
\N	1085	5249	GR	GR-81	Dodekanisos
\N	1085	5250	GR	GR-52	Drama
\N	1085	5251	GR	GR-71	Evros
\N	1085	5252	GR	GR-05	Evrytania
\N	1085	5253	GR	GR-04	Evvoia
\N	1085	5254	GR	GR-63	Florina
\N	1085	5255	GR	GR-07	Fokis
\N	1085	5256	GR	GR-06	Fthiotis
\N	1085	5257	GR	GR-51	Grevena
\N	1085	5258	GR	GR-14	Ileia
\N	1085	5259	GR	GR-53	Imathia
\N	1085	5260	GR	GR-33	Ioannina
\N	1085	5261	GR	GR-91	Irakleion
\N	1085	5262	GR	GR-41	Karditsa
\N	1085	5263	GR	GR-56	Kastoria
\N	1085	5264	GR	GR-55	Kavalla
\N	1085	5265	GR	GR-23	Kefallinia
\N	1085	5266	GR	GR-22	Kerkyra
\N	1085	5267	GR	GR-57	Kilkis
\N	1085	5268	GR	GR-15	Korinthia
\N	1085	5269	GR	GR-58	Kozani
\N	1085	5270	GR	GR-82	Kyklades
\N	1085	5271	GR	GR-16	Lakonia
\N	1085	5272	GR	GR-42	Larisa
\N	1085	5273	GR	GR-92	Lasithion
\N	1085	5274	GR	GR-24	Lefkas
\N	1085	5275	GR	GR-83	Lesvos
\N	1085	5276	GR	GR-43	Magnisia
\N	1085	5277	GR	GR-17	Messinia
\N	1085	5278	GR	GR-59	Pella
\N	1085	5279	GR	GR-34	Preveza
\N	1085	5280	GR	GR-93	Rethymnon
\N	1085	5281	GR	GR-73	Rodopi
\N	1085	5282	GR	GR-84	Samos
\N	1085	5283	GR	GR-62	Serrai
\N	1085	5284	GR	GR-32	Thesprotia
\N	1085	5285	GR	GR-54	Thessaloniki
\N	1085	5286	GR	GR-44	Trikala
\N	1085	5287	GR	GR-03	Voiotia
\N	1085	5288	GR	GR-72	Xanthi
\N	1085	5289	GR	GR-21	Zakynthos
\N	1085	5290	GR	GR-69	Agio Oros
\N	1090	5291	GT	GT-AV	Alta Verapez
\N	1090	5292	GT	GT-BV	Baja Verapez
\N	1090	5293	GT	GT-CM	Chimaltenango
\N	1090	5294	GT	GT-CQ	Chiquimula
\N	1090	5295	GT	GT-PR	El Progreso
\N	1090	5296	GT	GT-ES	Escuintla
\N	1090	5297	GT	GT-GU	Guatemala
\N	1090	5298	GT	GT-HU	Huehuetenango
\N	1090	5299	GT	GT-IZ	Izabal
\N	1090	5300	GT	GT-JA	Jalapa
\N	1090	5301	GT	GT-JU	Jutiapa
\N	1090	5302	GT	GT-PE	Peten
\N	1090	5303	GT	GT-QZ	Quetzaltenango
\N	1090	5304	GT	GT-QC	Quiche
\N	1090	5305	GT	GT-RE	Reta.thuleu
\N	1090	5306	GT	GT-SA	Sacatepequez
\N	1090	5307	GT	GT-SM	San Marcos
\N	1090	5308	GT	GT-SR	Santa Rosa
\N	1090	5309	GT	GT-SO	Solol6
\N	1090	5310	GT	GT-SU	Suchitepequez
\N	1090	5311	GT	GT-TO	Totonicapan
\N	1090	5312	GT	GT-ZA	Zacapa
\N	1092	5313	GW	GW-BS	Bissau
\N	1092	5314	GW	GW-BA	Bafata
\N	1092	5315	GW	GW-BM	Biombo
\N	1092	5316	GW	GW-BL	Bolama
\N	1092	5317	GW	GW-CA	Cacheu
\N	1092	5318	GW	GW-GA	Gabu
\N	1092	5319	GW	GW-OI	Oio
\N	1092	5320	GW	GW-QU	Quloara
\N	1092	5321	GW	GW-TO	Tombali S
\N	1093	5322	GY	GY-BA	Barima-Waini
\N	1093	5323	GY	GY-CU	Cuyuni-Mazaruni
\N	1093	5324	GY	GY-DE	Demerara-Mahaica
\N	1093	5325	GY	GY-EB	East Berbice-Corentyne
\N	1093	5326	GY	GY-ES	Essequibo Islands-West Demerara
\N	1093	5327	GY	GY-MA	Mahaica-Berbice
\N	1093	5328	GY	GY-PM	Pomeroon-Supenaam
\N	1093	5329	GY	GY-PT	Potaro-Siparuni
\N	1093	5330	GY	GY-UD	Upper Demerara-Berbice
\N	1093	5331	GY	GY-UT	Upper Takutu-Upper Essequibo
\N	1097	5332	HN	HN-AT	Atlantida
\N	1097	5333	HN	HN-CL	Colon
\N	1097	5334	HN	HN-CM	Comayagua
\N	1097	5335	HN	HN-CP	Copan
\N	1097	5336	HN	HN-CR	Cortes
\N	1097	5337	HN	HN-CH	Choluteca
\N	1097	5338	HN	HN-EP	El Paraiso
\N	1097	5339	HN	HN-FM	Francisco Morazan
\N	1097	5340	HN	HN-GD	Gracias a Dios
\N	1097	5341	HN	HN-IN	Intibuca
\N	1097	5342	HN	HN-IB	Islas de la Bahia
\N	1097	5343	HN	HN-LE	Lempira
\N	1097	5344	HN	HN-OC	Ocotepeque
\N	1097	5345	HN	HN-OL	Olancho
\N	1097	5346	HN	HN-SB	Santa Barbara
\N	1097	5347	HN	HN-VA	Valle
\N	1097	5348	HN	HN-YO	Yoro
\N	1055	5349	HR	HR-07	Bjelovarsko-bilogorska zupanija
\N	1055	5350	HR	HR-12	Brodsko-posavska zupanija
\N	1055	5351	HR	HR-19	Dubrovacko-neretvanska zupanija
\N	1055	5352	HR	HR-18	Istarska zupanija
\N	1055	5353	HR	HR-04	Karlovacka zupanija
\N	1055	5354	HR	HR-06	Koprivnickco-krizevacka zupanija
\N	1055	5355	HR	HR-02	Krapinako-zagorska zupanija
\N	1055	5356	HR	HR-09	Licko-senjska zupanija
\N	1055	5357	HR	HR-20	Medimurska zupanija
\N	1055	5358	HR	HR-14	Osjecko-baranjska zupanija
\N	1055	5359	HR	HR-11	Pozesko-slavonska zupanija
\N	1055	5360	HR	HR-08	Primorsko-goranska zupanija
\N	1055	5361	HR	HR-03	Sisacko-moelavacka Iupanija
\N	1055	5362	HR	HR-17	Splitako-dalmatinska zupanija
\N	1055	5363	HR	HR-15	Sibenako-kninska zupanija
\N	1055	5364	HR	HR-05	Varaidinska zupanija
\N	1055	5365	HR	HR-10	VirovitiEko-podravska zupanija
\N	1055	5366	HR	HR-16	VuRovarako-srijemska zupanija
\N	1055	5367	HR	HR-13	Zadaraka
\N	1055	5368	HR	HR-01	Zagrebacka zupanija
\N	1094	5369	HT	HT-GA	Grande-Anse
\N	1094	5370	HT	HT-NE	Nord-Eat
\N	1094	5371	HT	HT-NO	Nord-Ouest
\N	1094	5372	HT	HT-OU	Ouest
\N	1094	5373	HT	HT-SD	Sud
\N	1094	5374	HT	HT-SE	Sud-Est
\N	1099	5375	HU	HU-BU	Budapest
\N	1099	5376	HU	HU-BK	Bács-Kiskun
\N	1099	5377	HU	HU-BA	Baranya
\N	1099	5378	HU	HU-BE	Békés
\N	1099	5379	HU	HU-BZ	Borsod-Abaúj-Zemplén
\N	1099	5380	HU	HU-CS	Csongrád
\N	1099	5381	HU	HU-FE	Fejér
\N	1099	5382	HU	HU-GS	Győr-Moson-Sopron
\N	1099	5383	HU	HU-HB	Hajdu-Bihar
\N	1099	5384	HU	HU-HE	Heves
\N	1099	5385	HU	HU-JN	Jász-Nagykun-Szolnok
\N	1099	5386	HU	HU-KE	Komárom-Esztergom
\N	1099	5387	HU	HU-NO	Nográd
\N	1099	5388	HU	HU-PE	Pest
\N	1099	5389	HU	HU-SO	Somogy
\N	1099	5390	HU	HU-SZ	Szabolcs-Szatmár-Bereg
\N	1099	5391	HU	HU-TO	Tolna
\N	1099	5392	HU	HU-VA	Vas
\N	1099	5393	HU	HU-VE	Veszprém
\N	1099	5394	HU	HU-ZA	Zala
\N	1099	5395	HU	HU-BC	Békéscsaba
\N	1099	5396	HU	HU-DE	Debrecen
\N	1099	5397	HU	HU-DU	Dunaújváros
\N	1099	5398	HU	HU-EG	Eger
\N	1099	5399	HU	HU-GY	Győr
\N	1099	5400	HU	HU-HV	Hódmezővásárhely
\N	1099	5401	HU	HU-KV	Kaposvár
\N	1099	5402	HU	HU-KM	Kecskemét
\N	1099	5403	HU	HU-MI	Miskolc
\N	1099	5404	HU	HU-NK	Nagykanizsa
\N	1099	5405	HU	HU-NY	Nyiregyháza
\N	1099	5406	HU	HU-PS	Pécs
\N	1099	5407	HU	HU-ST	Salgótarján
\N	1099	5408	HU	HU-SN	Sopron
\N	1099	5409	HU	HU-SD	Szeged
\N	1099	5410	HU	HU-SF	Székesfehérvár
\N	1099	5411	HU	HU-SS	Szekszárd
\N	1099	5412	HU	HU-SK	Szolnok
\N	1099	5413	HU	HU-SH	Szombathely
\N	1099	5414	HU	HU-TB	Tatabánya
\N	1099	5415	HU	HU-ZE	Zalaegerszeg
\N	1102	5416	ID	ID-BA	Bali
\N	1102	5417	ID	ID-BB	Bangka Belitung
\N	1102	5418	ID	ID-BT	Banten
\N	1102	5419	ID	ID-BE	Bengkulu
\N	1102	5420	ID	ID-GO	Gorontalo
\N	1102	5421	ID	ID-IJ	Irian Jaya
\N	1102	5422	ID	ID-JA	Jambi
\N	1102	5423	ID	ID-JB	Jawa Barat
\N	1102	5424	ID	ID-JT	Jawa Tengah
\N	1102	5425	ID	ID-JI	Jawa Timur
\N	1102	5426	ID	ID-KB	Kalimantan Barat
\N	1102	5427	ID	ID-KT	Kalimantan Timur
\N	1102	5428	ID	ID-KS	Kalimantan Selatan
\N	1102	5429	ID	ID-KR	Kepulauan Riau
\N	1102	5430	ID	ID-LA	Lampung
\N	1102	5431	ID	ID-MA	Maluku
\N	1102	5432	ID	ID-MU	Maluku Utara
\N	1102	5433	ID	ID-NB	Nusa Tenggara Barat
\N	1102	5434	ID	ID-NT	Nusa Tenggara Timur
\N	1102	5435	ID	ID-PA	Papua
\N	1102	5436	ID	ID-RI	Riau
\N	1102	5437	ID	ID-SN	Sulawesi Selatan
\N	1102	5438	ID	ID-ST	Sulawesi Tengah
\N	1102	5439	ID	ID-SG	Sulawesi Tenggara
\N	1102	5440	ID	ID-SA	Sulawesi Utara
\N	1102	5441	ID	ID-SB	Sumatra Barat
\N	1102	5442	ID	ID-SS	Sumatra Selatan
\N	1102	5443	ID	ID-SU	Sumatera Utara
\N	1102	5444	ID	ID-JK	Jakarta Raya
\N	1102	5445	ID	ID-AC	Aceh
\N	1102	5446	ID	ID-YO	Yogyakarta
\N	1105	5447	IE	IE-C	Cork
\N	1105	5448	IE	IE-CE	Clare
\N	1105	5449	IE	IE-CN	Cavan
\N	1105	5450	IE	IE-CW	Carlow
\N	1105	5451	IE	IE-D	Dublin
\N	1105	5452	IE	IE-DL	Donegal
\N	1105	5453	IE	IE-G	Galway
\N	1105	5454	IE	IE-KE	Kildare
\N	1105	5455	IE	IE-KK	Kilkenny
\N	1105	5456	IE	IE-KY	Kerry
\N	1105	5457	IE	IE-LD	Longford
\N	1105	5458	IE	IE-LH	Louth
\N	1105	5459	IE	IE-LK	Limerick
\N	1105	5460	IE	IE-LM	Leitrim
\N	1105	5461	IE	IE-LS	Laois
\N	1105	5462	IE	IE-MH	Meath
\N	1105	5463	IE	IE-MN	Monaghan
\N	1105	5464	IE	IE-MO	Mayo
\N	1105	5465	IE	IE-OY	Offaly
\N	1105	5466	IE	IE-RN	Roscommon
\N	1105	5467	IE	IE-SO	Sligo
\N	1105	5468	IE	IE-TA	Tipperary
\N	1105	5469	IE	IE-WD	Waterford
\N	1105	5470	IE	IE-WH	Westmeath
\N	1105	5471	IE	IE-WW	Wicklow
\N	1105	5472	IE	IE-WX	Wexford
\N	1106	5473	IL	IL-D	HaDarom
\N	1106	5474	IL	IL-M	HaMerkaz
\N	1106	5475	IL	IL-Z	HaZafon
\N	1106	5476	IL	IL-HA	Hefa
\N	1106	5477	IL	IL-TA	Tel-Aviv
\N	1106	5478	IL	IL-JM	Yerushalayim Al Quds
1202	1101	5479	IN	IN-AP	Andhra Pradesh
1203	1101	5480	IN	IN-AR	Arunachal Pradesh
1204	1101	5481	IN	IN-AS	Assam
1205	1101	5482	IN	IN-BR	Bihar
1206	1101	5483	IN	IN-CH	Chhattisgarh
1207	1101	5484	IN	IN-GA	Goa
1208	1101	5485	IN	IN-GJ	Gujarat
1209	1101	5486	IN	IN-HR	Haryana
1210	1101	5487	IN	IN-HP	Himachal Pradesh
1211	1101	5488	IN	IN-JK	Jammu and Kashmir
1212	1101	5489	IN	IN-JH	Jharkhand
1213	1101	5491	IN	IN-KL	Kerala
1214	1101	5492	IN	IN-MP	Madhya Pradesh
1215	1101	5494	IN	IN-MN	Manipur
1216	1101	5495	IN	IN-ML	Meghalaya
1217	1101	5496	IN	IN-MZ	Mizoram
1218	1101	5497	IN	IN-NL	Nagaland
1219	1101	5498	IN	IN-OR	Orissa
1220	1101	5499	IN	IN-PB	Punjab
1221	1101	5500	IN	IN-RJ	Rajasthan
1222	1101	5501	IN	IN-SK	Sikkim
1223	1101	5502	IN	IN-TN	Tamil Nadu
1224	1101	5503	IN	IN-TR	Tripura
1225	1101	5504	IN	IN-UL	Uttaranchal
1226	1101	5505	IN	IN-UP	Uttar Pradesh
1227	1101	5506	IN	IN-WB	West Bengal
1228	1101	5507	IN	IN-AN	Andaman and Nicobar Islands
1229	1101	5508	IN	IN-DN	Dadra and Nagar Haveli
1230	1101	5509	IN	IN-DD	Daman and Diu
1231	1101	5510	IN	IN-DL	Delhi
1232	1101	5511	IN	IN-LD	Lakshadweep
1233	1101	5512	IN	IN-PY	Pondicherry
\N	1104	5513	IQ	IQ-AN	Al Anbar
\N	1104	5514	IQ	IQ-BA	Al Ba,rah
\N	1104	5515	IQ	IQ-MU	Al Muthanna
\N	1104	5516	IQ	IQ-QA	Al Qadisiyah
\N	1104	5517	IQ	IQ-NA	An Najef
\N	1104	5518	IQ	IQ-AR	Arbil
\N	1104	5519	IQ	IQ-SW	As Sulaymaniyah
\N	1104	5520	IQ	IQ-TS	At Ta'mim
\N	1104	5521	IQ	IQ-BB	Babil
\N	1104	5522	IQ	IQ-BG	Baghdad
\N	1104	5523	IQ	IQ-DA	Dahuk
\N	1104	5524	IQ	IQ-DQ	Dhi Qar
\N	1104	5525	IQ	IQ-DI	Diyala
\N	1104	5526	IQ	IQ-KA	Karbala'
\N	1104	5527	IQ	IQ-MA	Maysan
\N	1104	5528	IQ	IQ-NI	Ninawa
\N	1104	5529	IQ	IQ-SD	Salah ad Din
\N	1104	5530	IQ	IQ-WA	Wasit
\N	1103	5531	IR	IR-03	Ardabil
\N	1103	5532	IR	IR-02	Azarbayjan-e Gharbi
\N	1103	5533	IR	IR-01	Azarbayjan-e Sharqi
\N	1103	5534	IR	IR-06	Bushehr
\N	1103	5535	IR	IR-08	Chahar Mahall va Bakhtiari
\N	1103	5536	IR	IR-04	Esfahan
\N	1103	5537	IR	IR-14	Fars
\N	1103	5538	IR	IR-19	Gilan
\N	1103	5539	IR	IR-27	Golestan
\N	1103	5540	IR	IR-24	Hamadan
\N	1103	5541	IR	IR-23	Hormozgan
\N	1103	5542	IR	IR-05	Iiam
\N	1103	5543	IR	IR-15	Kerman
\N	1103	5544	IR	IR-17	Kermanshah
\N	1103	5545	IR	IR-09	Khorasan
\N	1103	5546	IR	IR-10	Khuzestan
\N	1103	5547	IR	IR-18	Kohjiluyeh va Buyer Ahmad
\N	1103	5548	IR	IR-16	Kordestan
\N	1103	5549	IR	IR-20	Lorestan
\N	1103	5550	IR	IR-22	Markazi
\N	1103	5551	IR	IR-21	Mazandaran
\N	1103	5552	IR	IR-28	Qazvin
\N	1103	5553	IR	IR-26	Qom
\N	1103	5554	IR	IR-12	Semnan
\N	1103	5555	IR	IR-13	Sistan va Baluchestan
\N	1103	5556	IR	IR-07	Tehran
\N	1103	5557	IR	IR-25	Yazd
\N	1103	5558	IR	IR-11	Zanjan
\N	1100	5559	IS	IS-7	Austurland
\N	1100	5560	IS	IS-1	Hofuoborgarsvaeoi utan Reykjavikur
\N	1100	5561	IS	IS-6	Norourland eystra
\N	1100	5562	IS	IS-5	Norourland vestra
\N	1100	5563	IS	IS-0	Reykjavik
\N	1100	5564	IS	IS-8	Suourland
\N	1100	5565	IS	IS-2	Suournes
\N	1100	5566	IS	IS-4	Vestfirolr
\N	1100	5567	IS	IS-3	Vesturland
\N	1107	5568	IT	IT-AG	Agrigento
\N	1107	5569	IT	IT-AL	Alessandria
\N	1107	5570	IT	IT-AN	Ancona
\N	1107	5571	IT	IT-AO	Aosta
\N	1107	5572	IT	IT-AR	Arezzo
\N	1107	5573	IT	IT-AP	Ascoli Piceno
\N	1107	5574	IT	IT-AT	Asti
\N	1107	5575	IT	IT-AV	Avellino
\N	1107	5576	IT	IT-BA	Bari
\N	1107	5577	IT	IT-BL	Belluno
\N	1107	5578	IT	IT-BN	Benevento
\N	1107	5579	IT	IT-BG	Bergamo
\N	1107	5580	IT	IT-BI	Biella
\N	1107	5581	IT	IT-BO	Bologna
\N	1107	5582	IT	IT-BZ	Bolzano
\N	1107	5583	IT	IT-BS	Brescia
\N	1107	5584	IT	IT-BR	Brindisi
\N	1107	5585	IT	IT-CA	Cagliari
\N	1107	5586	IT	IT-CL	Caltanissetta
\N	1107	5587	IT	IT-CB	Campobasso
\N	1107	5588	IT	IT-CE	Caserta
\N	1107	5589	IT	IT-CT	Catania
\N	1107	5590	IT	IT-CZ	Catanzaro
\N	1107	5591	IT	IT-CH	Chieti
\N	1107	5592	IT	IT-CO	Como
\N	1107	5593	IT	IT-CS	Cosenza
\N	1107	5594	IT	IT-CR	Cremona
\N	1107	5595	IT	IT-KR	Crotone
\N	1107	5596	IT	IT-CN	Cuneo
\N	1107	5597	IT	IT-EN	Enna
\N	1107	5598	IT	IT-FE	Ferrara
\N	1107	5599	IT	IT-FI	Firenze
\N	1107	5600	IT	IT-FG	Foggia
\N	1107	5601	IT	IT-FO	Forli
\N	1107	5602	IT	IT-FR	Frosinone
\N	1107	5603	IT	IT-GE	Genova
\N	1107	5604	IT	IT-GO	Gorizia
\N	1107	5605	IT	IT-GR	Grosseto
\N	1107	5606	IT	IT-IM	Imperia
\N	1107	5607	IT	IT-IS	Isernia
\N	1107	5608	IT	IT-AQ	L'Aquila
\N	1107	5609	IT	IT-SP	La Spezia
\N	1107	5610	IT	IT-LT	Latina
\N	1107	5611	IT	IT-LE	Lecce
\N	1107	5612	IT	IT-LC	Lecco
\N	1107	5613	IT	IT-LI	Livorno
\N	1107	5614	IT	IT-LO	Lodi
\N	1107	5615	IT	IT-LU	Lucca
\N	1107	5616	IT	IT-SC	Macerata
\N	1107	5617	IT	IT-MN	Mantova
\N	1107	5618	IT	IT-MS	Massa-Carrara
\N	1107	5619	IT	IT-MT	Matera
\N	1107	5620	IT	IT-ME	Messina
\N	1107	5621	IT	IT-MI	Milano
\N	1107	5622	IT	IT-MO	Modena
\N	1107	5623	IT	IT-NA	Napoli
\N	1107	5624	IT	IT-NO	Novara
\N	1107	5625	IT	IT-NU	Nuoro
\N	1107	5626	IT	IT-OR	Oristano
\N	1107	5627	IT	IT-PD	Padova
\N	1107	5628	IT	IT-PA	Palermo
\N	1107	5629	IT	IT-PR	Parma
\N	1107	5630	IT	IT-PV	Pavia
\N	1107	5631	IT	IT-PG	Perugia
\N	1107	5632	IT	IT-PS	Pesaro e Urbino
\N	1107	5633	IT	IT-PE	Pescara
\N	1107	5634	IT	IT-PC	Piacenza
\N	1107	5635	IT	IT-PI	Pisa
\N	1107	5636	IT	IT-PT	Pistoia
\N	1107	5637	IT	IT-PN	Pordenone
\N	1107	5638	IT	IT-PZ	Potenza
\N	1107	5639	IT	IT-PO	Prato
\N	1107	5640	IT	IT-RG	Ragusa
\N	1107	5641	IT	IT-RA	Ravenna
\N	1107	5642	IT	IT-RC	Reggio Calabria
\N	1107	5643	IT	IT-RE	Reggio Emilia
\N	1107	5644	IT	IT-RI	Rieti
\N	1107	5645	IT	IT-RN	Rimini
\N	1107	5646	IT	IT-RM	Roma
\N	1107	5647	IT	IT-RO	Rovigo
\N	1107	5648	IT	IT-SA	Salerno
\N	1107	5649	IT	IT-SS	Sassari
\N	1107	5650	IT	IT-SV	Savona
\N	1107	5651	IT	IT-SI	Siena
\N	1107	5652	IT	IT-SR	Siracusa
\N	1107	5653	IT	IT-SO	Sondrio
\N	1107	5654	IT	IT-TA	Taranto
\N	1107	5655	IT	IT-TE	Teramo
\N	1107	5656	IT	IT-TR	Terni
\N	1107	5657	IT	IT-TO	Torino
\N	1107	5658	IT	IT-TP	Trapani
\N	1107	5659	IT	IT-TN	Trento
\N	1107	5660	IT	IT-TV	Treviso
\N	1107	5661	IT	IT-TS	Trieste
\N	1107	5662	IT	IT-UD	Udine
\N	1107	5663	IT	IT-VA	Varese
\N	1107	5664	IT	IT-VE	Venezia
\N	1107	5665	IT	IT-VB	Verbano-Cusio-Ossola
\N	1107	5666	IT	IT-VC	Vercelli
\N	1107	5667	IT	IT-VR	Verona
\N	1107	5668	IT	IT-VV	Vibo Valentia
\N	1107	5669	IT	IT-VI	Vicenza
\N	1107	5670	IT	IT-VT	Viterbo
\N	1109	5671	JP	JP-23	Aichi
\N	1109	5672	JP	JP-05	Akita
\N	1109	5673	JP	JP-02	Aomori
\N	1109	5674	JP	JP-12	Chiba
\N	1109	5675	JP	JP-38	Ehime
\N	1109	5676	JP	JP-18	Fukui
\N	1109	5677	JP	JP-40	Fukuoka
\N	1109	5678	JP	JP-07	Fukusima
\N	1109	5679	JP	JP-21	Gifu
\N	1109	5680	JP	JP-10	Gunma
\N	1109	5681	JP	JP-34	Hiroshima
\N	1109	5682	JP	JP-01	Hokkaido
\N	1109	5683	JP	JP-28	Hyogo
\N	1109	5684	JP	JP-08	Ibaraki
\N	1109	5685	JP	JP-17	Ishikawa
\N	1109	5686	JP	JP-03	Iwate
\N	1109	5687	JP	JP-37	Kagawa
\N	1109	5688	JP	JP-46	Kagoshima
\N	1109	5689	JP	JP-14	Kanagawa
\N	1109	5690	JP	JP-39	Kochi
\N	1109	5691	JP	JP-43	Kumamoto
\N	1109	5692	JP	JP-26	Kyoto
\N	1109	5693	JP	JP-24	Mie
\N	1109	5694	JP	JP-04	Miyagi
\N	1109	5695	JP	JP-45	Miyazaki
\N	1109	5696	JP	JP-20	Nagano
\N	1109	5697	JP	JP-42	Nagasaki
\N	1109	5698	JP	JP-29	Nara
\N	1109	5699	JP	JP-15	Niigata
\N	1109	5700	JP	JP-44	Oita
\N	1109	5701	JP	JP-33	Okayama
\N	1109	5702	JP	JP-47	Okinawa
\N	1109	5703	JP	JP-27	Osaka
\N	1109	5704	JP	JP-41	Saga
\N	1109	5705	JP	JP-11	Saitama
\N	1109	5706	JP	JP-25	Shiga
\N	1109	5707	JP	JP-32	Shimane
\N	1109	5708	JP	JP-22	Shizuoka
\N	1109	5709	JP	JP-09	Tochigi
\N	1109	5710	JP	JP-36	Tokushima
\N	1109	5711	JP	JP-13	Tokyo
\N	1109	5712	JP	JP-31	Tottori
\N	1109	5713	JP	JP-16	Toyama
\N	1109	5714	JP	JP-30	Wakayama
\N	1109	5715	JP	JP-06	Yamagata
\N	1109	5716	JP	JP-35	Yamaguchi
\N	1109	5717	JP	JP-19	Yamanashi
\N	1108	5718	JM	JM-13	Clarendon
\N	1108	5719	JM	JM-09	Hanover
\N	1108	5720	JM	JM-01	Kingston
\N	1108	5721	JM	JM-04	Portland
\N	1108	5722	JM	JM-02	Saint Andrew
\N	1108	5723	JM	JM-06	Saint Ann
\N	1108	5724	JM	JM-14	Saint Catherine
\N	1108	5725	JM	JM-11	Saint Elizabeth
\N	1108	5726	JM	JM-08	Saint James
\N	1108	5727	JM	JM-05	Saint Mary
\N	1108	5728	JM	JM-03	Saint Thomea
\N	1108	5729	JM	JM-07	Trelawny
\N	1108	5730	JM	JM-10	Westmoreland
\N	1110	5731	JO	JO-AJ	Ajln
\N	1110	5732	JO	JO-AQ	Al 'Aqaba
\N	1110	5733	JO	JO-BA	Al Balqa'
\N	1110	5734	JO	JO-KA	Al Karak
\N	1110	5735	JO	JO-MA	Al Mafraq
\N	1110	5736	JO	JO-AM	Amman
\N	1110	5737	JO	JO-AT	At Tafilah
\N	1110	5738	JO	JO-AZ	Az Zarga
\N	1110	5739	JO	JO-JR	Irbid
\N	1110	5740	JO	JO-JA	Jarash
\N	1110	5741	JO	JO-MN	Ma'an
\N	1110	5742	JO	JO-MD	Madaba
\N	1112	5743	KE	KE-110	Nairobi Municipality
\N	1112	5744	KE	KE-300	Coast
\N	1112	5745	KE	KE-500	North-Eastern Kaskazini Mashariki
\N	1112	5746	KE	KE-700	Rift Valley
\N	1112	5747	KE	KE-900	Western Magharibi
\N	1117	5748	KG	KG-GB	Bishkek
\N	1117	5749	KG	KG-B	Batken
\N	1117	5750	KG	KG-C	Chu
\N	1117	5751	KG	KG-J	Jalal-Abad
\N	1117	5752	KG	KG-N	Naryn
\N	1117	5753	KG	KG-O	Osh
\N	1117	5754	KG	KG-T	Talas
\N	1117	5755	KG	KG-Y	Ysyk-Kol
\N	1037	5756	KH	KH-23	Krong Kaeb
\N	1037	5757	KH	KH-24	Krong Pailin
\N	1037	5758	KH	KH-18	Xrong Preah Sihanouk
\N	1037	5759	KH	KH-12	Phnom Penh
\N	1037	5760	KH	KH-2	Baat Dambang
\N	1037	5761	KH	KH-1	Banteay Mean Chey
\N	1037	5762	KH	KH-3	Rampong Chaam
\N	1037	5763	KH	KH-4	Kampong Chhnang
\N	1037	5764	KH	KH-5	Kampong Spueu
\N	1037	5765	KH	KH-6	Kampong Thum
\N	1037	5766	KH	KH-7	Kampot
\N	1037	5767	KH	KH-8	Kandaal
\N	1037	5768	KH	KH-9	Kach Kong
\N	1037	5769	KH	KH-10	Krachoh
\N	1037	5770	KH	KH-11	Mondol Kiri
\N	1037	5771	KH	KH-22	Otdar Mean Chey
\N	1037	5772	KH	KH-15	Pousaat
\N	1037	5773	KH	KH-13	Preah Vihear
\N	1037	5774	KH	KH-14	Prey Veaeng
\N	1037	5775	KH	KH-16	Rotanak Kiri
\N	1037	5776	KH	KH-17	Siem Reab
\N	1037	5777	KH	KH-19	Stueng Traeng
\N	1037	5778	KH	KH-20	Svaay Rieng
\N	1037	5779	KH	KH-21	Taakaev
\N	1113	5780	KI	KI-G	Gilbert Islands
\N	1113	5781	KI	KI-L	Line Islands
\N	1113	5782	KI	KI-P	Phoenix Islands
\N	1049	5783	KM	KM-A	Anjouan Ndzouani
\N	1049	5784	KM	KM-G	Grande Comore Ngazidja
\N	1049	5785	KM	KM-M	Moheli Moili
\N	1114	5786	KP	KP-KAE	Kaesong-si
\N	1114	5787	KP	KP-NAM	Nampo-si
\N	1114	5788	KP	KP-PYO	Pyongyang-ai
\N	1114	5789	KP	KP-CHA	Chagang-do
\N	1114	5790	KP	KP-HAB	Hamgyongbuk-do
\N	1114	5791	KP	KP-HAN	Hamgyongnam-do
\N	1114	5792	KP	KP-HWB	Hwanghaebuk-do
\N	1114	5793	KP	KP-HWN	Hwanghaenam-do
\N	1114	5794	KP	KP-KAN	Kangwon-do
\N	1114	5795	KP	KP-PYB	Pyonganbuk-do
\N	1114	5796	KP	KP-PYN	Pyongannam-do
\N	1114	5797	KP	KP-YAN	Yanggang-do
\N	1114	5798	KP	KP-NAJ	Najin Sonbong-si
\N	1115	5799	KR	KR-11	Seoul Teugbyeolsi
\N	1115	5800	KR	KR-26	Busan Gwang'yeogsi
\N	1115	5801	KR	KR-27	Daegu Gwang'yeogsi
\N	1115	5802	KR	KR-30	Daejeon Gwang'yeogsi
\N	1115	5803	KR	KR-29	Gwangju Gwang'yeogsi
\N	1115	5804	KR	KR-28	Incheon Gwang'yeogsi
\N	1115	5805	KR	KR-31	Ulsan Gwang'yeogsi
\N	1115	5806	KR	KR-43	Chungcheongbugdo
\N	1115	5807	KR	KR-44	Chungcheongnamdo
\N	1115	5808	KR	KR-42	Gang'weondo
\N	1115	5809	KR	KR-41	Gyeonggido
\N	1115	5810	KR	KR-47	Gyeongsangbugdo
\N	1115	5811	KR	KR-48	Gyeongsangnamdo
\N	1115	5812	KR	KR-49	Jejudo
\N	1115	5813	KR	KR-45	Jeonrabugdo
\N	1115	5814	KR	KR-46	Jeonranamdo
\N	1116	5815	KW	KW-AH	Al Ahmadi
\N	1116	5816	KW	KW-FA	Al Farwanlyah
\N	1116	5817	KW	KW-JA	Al Jahrah
\N	1116	5818	KW	KW-KU	Al Kuwayt
\N	1116	5819	KW	KW-HA	Hawalli
\N	1111	5820	KZ	KZ-ALA	Almaty
\N	1111	5821	KZ	KZ-AST	Astana
\N	1111	5822	KZ	KZ-ALM	Almaty oblysy
\N	1111	5823	KZ	KZ-AKM	Aqmola oblysy
\N	1111	5824	KZ	KZ-AKT	Aqtobe oblysy
\N	1111	5825	KZ	KZ-ATY	Atyrau oblyfiy
\N	1111	5826	KZ	KZ-ZAP	Batys Quzaqstan oblysy
\N	1111	5827	KZ	KZ-MAN	Mangghystau oblysy
\N	1111	5828	KZ	KZ-YUZ	Ongtustik Quzaqstan oblysy
\N	1111	5829	KZ	KZ-PAV	Pavlodar oblysy
\N	1111	5830	KZ	KZ-KAR	Qaraghandy oblysy
\N	1111	5831	KZ	KZ-KUS	Qostanay oblysy
\N	1111	5832	KZ	KZ-KZY	Qyzylorda oblysy
\N	1111	5833	KZ	KZ-VOS	Shyghys Quzaqstan oblysy
\N	1111	5834	KZ	KZ-SEV	Soltustik Quzaqstan oblysy
\N	1111	5835	KZ	KZ-ZHA	Zhambyl oblysy Zhambylskaya oblast'
\N	1118	5836	LA	LA-VT	Vientiane
\N	1118	5837	LA	LA-AT	Attapu
\N	1118	5838	LA	LA-BK	Bokeo
\N	1118	5839	LA	LA-BL	Bolikhamxai
\N	1118	5840	LA	LA-CH	Champasak
\N	1118	5841	LA	LA-HO	Houaphan
\N	1118	5842	LA	LA-KH	Khammouan
\N	1118	5843	LA	LA-LM	Louang Namtha
\N	1118	5844	LA	LA-LP	Louangphabang
\N	1118	5845	LA	LA-OU	Oudomxai
\N	1118	5846	LA	LA-PH	Phongsali
\N	1118	5847	LA	LA-SL	Salavan
\N	1118	5848	LA	LA-SV	Savannakhet
\N	1118	5849	LA	LA-XA	Xaignabouli
\N	1118	5850	LA	LA-XN	Xiasomboun
\N	1118	5851	LA	LA-XE	Xekong
\N	1118	5852	LA	LA-XI	Xiangkhoang
\N	1120	5853	LB	LB-BA	Beirout
\N	1120	5854	LB	LB-BI	El Begsa
\N	1120	5855	LB	LB-JL	Jabal Loubnane
\N	1120	5856	LB	LB-AS	Loubnane ech Chemali
\N	1120	5857	LB	LB-JA	Loubnane ej Jnoubi
\N	1120	5858	LB	LB-NA	Nabatiye
\N	1199	5859	LK	LK-52	Ampara
\N	1199	5860	LK	LK-71	Anuradhapura
\N	1199	5861	LK	LK-81	Badulla
\N	1199	5862	LK	LK-51	Batticaloa
\N	1199	5863	LK	LK-11	Colombo
\N	1199	5864	LK	LK-31	Galle
\N	1199	5865	LK	LK-12	Gampaha
\N	1199	5866	LK	LK-33	Hambantota
\N	1199	5867	LK	LK-41	Jaffna
\N	1199	5868	LK	LK-13	Kalutara
\N	1199	5869	LK	LK-21	Kandy
\N	1199	5870	LK	LK-92	Kegalla
\N	1199	5871	LK	LK-42	Kilinochchi
\N	1199	5872	LK	LK-61	Kurunegala
\N	1199	5873	LK	LK-43	Mannar
\N	1199	5874	LK	LK-22	Matale
\N	1199	5875	LK	LK-32	Matara
\N	1199	5876	LK	LK-82	Monaragala
\N	1199	5877	LK	LK-45	Mullaittivu
\N	1199	5878	LK	LK-23	Nuwara Eliya
\N	1199	5879	LK	LK-72	Polonnaruwa
\N	1199	5880	LK	LK-62	Puttalum
\N	1199	5881	LK	LK-91	Ratnapura
\N	1199	5882	LK	LK-53	Trincomalee
\N	1199	5883	LK	LK-44	VavunLya
\N	1122	5884	LR	LR-BM	Bomi
\N	1122	5885	LR	LR-BG	Bong
\N	1122	5886	LR	LR-GB	Grand Basaa
\N	1122	5887	LR	LR-CM	Grand Cape Mount
\N	1122	5888	LR	LR-GG	Grand Gedeh
\N	1122	5889	LR	LR-GK	Grand Kru
\N	1122	5890	LR	LR-LO	Lofa
\N	1122	5891	LR	LR-MG	Margibi
\N	1122	5892	LR	LR-MY	Maryland
\N	1122	5893	LR	LR-MO	Montserrado
\N	1122	5894	LR	LR-NI	Nimba
\N	1122	5895	LR	LR-RI	Rivercess
\N	1122	5896	LR	LR-SI	Sinoe
\N	1121	5897	LS	LS-D	Berea
\N	1121	5898	LS	LS-B	Butha-Buthe
\N	1121	5899	LS	LS-C	Leribe
\N	1121	5900	LS	LS-E	Mafeteng
\N	1121	5901	LS	LS-A	Maseru
\N	1121	5902	LS	LS-F	Mohale's Hoek
\N	1121	5903	LS	LS-J	Mokhotlong
\N	1121	5904	LS	LS-H	Qacha's Nek
\N	1121	5905	LS	LS-G	Quthing
\N	1121	5906	LS	LS-K	Thaba-Tseka
\N	1125	5907	LT	LT-AL	Alytaus Apskritis
\N	1125	5908	LT	LT-KU	Kauno Apskritis
\N	1125	5909	LT	LT-KL	Klaipedos Apskritis
\N	1125	5910	LT	LT-MR	Marijampoles Apskritis
\N	1125	5911	LT	LT-PN	Panevezio Apskritis
\N	1125	5912	LT	LT-SA	Sisuliu Apskritis
\N	1125	5913	LT	LT-TA	Taurages Apskritis
\N	1125	5914	LT	LT-TE	Telsiu Apskritis
\N	1125	5915	LT	LT-UT	Utenos Apskritis
\N	1125	5916	LT	LT-VL	Vilniaus Apskritis
\N	1126	5917	LU	LU-D	Diekirch
\N	1126	5918	LU	LU-G	GreveNmacher
\N	1119	5919	LV	LV-AI	Aizkraukles Apripkis
\N	1119	5920	LV	LV-AL	Alkanes Apripkis
\N	1119	5921	LV	LV-BL	Balvu Apripkis
\N	1119	5922	LV	LV-BU	Bauskas Apripkis
\N	1119	5923	LV	LV-CE	Cesu Aprikis
\N	1119	5924	LV	LV-DA	Daugavpile Apripkis
\N	1119	5925	LV	LV-DO	Dobeles Apripkis
\N	1119	5926	LV	LV-GU	Gulbenes Aprlpkis
\N	1119	5927	LV	LV-JL	Jelgavas Apripkis
\N	1119	5928	LV	LV-JK	Jekabpils Apripkis
\N	1119	5929	LV	LV-KR	Kraslavas Apripkis
\N	1119	5930	LV	LV-KU	Kuldlgas Apripkis
\N	1119	5931	LV	LV-LM	Limbazu Apripkis
\N	1119	5932	LV	LV-LE	Liepajas Apripkis
\N	1119	5933	LV	LV-LU	Ludzas Apripkis
\N	1119	5934	LV	LV-MA	Madonas Apripkis
\N	1119	5935	LV	LV-OG	Ogres Apripkis
\N	1119	5936	LV	LV-PR	Preilu Apripkis
\N	1119	5937	LV	LV-RE	Rezaknes Apripkis
\N	1119	5938	LV	LV-RI	Rigas Apripkis
\N	1119	5939	LV	LV-SA	Saldus Apripkis
\N	1119	5940	LV	LV-TA	Talsu Apripkis
\N	1119	5941	LV	LV-TU	Tukuma Apriplcis
\N	1119	5942	LV	LV-VK	Valkas Apripkis
\N	1119	5943	LV	LV-VM	Valmieras Apripkis
\N	1119	5944	LV	LV-VE	Ventspils Apripkis
\N	1119	5945	LV	LV-DGV	Daugavpils
\N	1119	5946	LV	LV-JEL	Jelgava
\N	1119	5947	LV	LV-JUR	Jurmala
\N	1119	5948	LV	LV-LPX	Liepaja
\N	1119	5949	LV	LV-REZ	Rezekne
\N	1119	5950	LV	LV-RIX	Riga
\N	1119	5951	LV	LV-VEN	Ventspils
\N	1123	5952	LY	LY-AJ	Ajdābiyā
\N	1123	5953	LY	LY-BU	Al Buţnān
\N	1123	5954	LY	LY-HZ	Al Hizām al Akhdar
\N	1123	5955	LY	LY-JA	Al Jabal al Akhdar
\N	1123	5956	LY	LY-JI	Al Jifārah
\N	1123	5957	LY	LY-JU	Al Jufrah
\N	1123	5958	LY	LY-KF	Al Kufrah
\N	1123	5959	LY	LY-MJ	Al Marj
\N	1123	5960	LY	LY-MB	Al Marqab
\N	1123	5961	LY	LY-QT	Al Qaţrūn
\N	1123	5962	LY	LY-QB	Al Qubbah
\N	1123	5963	LY	LY-WA	Al Wāhah
\N	1123	5964	LY	LY-NQ	An Nuqaţ al Khams
\N	1123	5965	LY	LY-SH	Ash Shāţi'
\N	1123	5966	LY	LY-ZA	Az Zāwiyah
\N	1123	5967	LY	LY-BA	Banghāzī
\N	1123	5968	LY	LY-BW	Banī Walīd
\N	1123	5969	LY	LY-DR	Darnah
\N	1123	5970	LY	LY-GD	Ghadāmis
\N	1123	5971	LY	LY-GR	Gharyān
\N	1123	5972	LY	LY-GT	Ghāt
\N	1123	5973	LY	LY-JB	Jaghbūb
\N	1123	5974	LY	LY-MI	Mişrātah
\N	1123	5975	LY	LY-MZ	Mizdah
\N	1123	5976	LY	LY-MQ	Murzuq
\N	1123	5977	LY	LY-NL	Nālūt
\N	1123	5978	LY	LY-SB	Sabhā
\N	1123	5979	LY	LY-SS	Şabrātah Şurmān
\N	1123	5980	LY	LY-SR	Surt
\N	1123	5981	LY	LY-TN	Tājūrā' wa an Nawāhī al Arbāh
\N	1123	5982	LY	LY-TB	Ţarābulus
\N	1123	5983	LY	LY-TM	Tarhūnah-Masallātah
\N	1123	5984	LY	LY-WD	Wādī al hayāt
\N	1123	5985	LY	LY-YJ	Yafran-Jādū
\N	1146	5986	MA	MA-AGD	Agadir
\N	1146	5987	MA	MA-BAH	Aït Baha
\N	1146	5988	MA	MA-MEL	Aït Melloul
\N	1146	5989	MA	MA-HAO	Al Haouz
\N	1146	5990	MA	MA-HOC	Al Hoceïma
\N	1146	5991	MA	MA-ASZ	Assa-Zag
\N	1146	5992	MA	MA-AZI	Azilal
\N	1146	5993	MA	MA-BEM	Beni Mellal
\N	1146	5994	MA	MA-BES	Ben Sllmane
\N	1146	5995	MA	MA-BER	Berkane
\N	1146	5996	MA	MA-BOD	Boujdour
\N	1146	5997	MA	MA-BOM	Boulemane
\N	1146	5998	MA	MA-CAS	Casablanca  [Dar el Beïda]
\N	1146	5999	MA	MA-CHE	Chefchaouene
\N	1146	6000	MA	MA-CHI	Chichaoua
\N	1146	6001	MA	MA-HAJ	El Hajeb
\N	1146	6002	MA	MA-JDI	El Jadida
\N	1146	6003	MA	MA-ERR	Errachidia
\N	1146	6004	MA	MA-ESI	Essaouira
\N	1146	6005	MA	MA-ESM	Es Smara
\N	1146	6006	MA	MA-FES	Fès
\N	1146	6007	MA	MA-FIG	Figuig
\N	1146	6008	MA	MA-GUE	Guelmim
\N	1146	6009	MA	MA-IFR	Ifrane
\N	1146	6010	MA	MA-JRA	Jerada
\N	1146	6011	MA	MA-KES	Kelaat Sraghna
\N	1146	6012	MA	MA-KEN	Kénitra
\N	1146	6013	MA	MA-KHE	Khemisaet
\N	1146	6014	MA	MA-KHN	Khenifra
\N	1146	6015	MA	MA-KHO	Khouribga
\N	1146	6016	MA	MA-LAA	Laâyoune (EH)
\N	1146	6017	MA	MA-LAP	Larache
\N	1146	6018	MA	MA-MAR	Marrakech
\N	1146	6019	MA	MA-MEK	Meknsès
\N	1146	6020	MA	MA-NAD	Nador
\N	1146	6021	MA	MA-OUA	Ouarzazate
\N	1146	6022	MA	MA-OUD	Oued ed Dahab (EH)
\N	1146	6023	MA	MA-OUJ	Oujda
\N	1146	6024	MA	MA-RBA	Rabat-Salé
\N	1146	6025	MA	MA-SAF	Safi
\N	1146	6026	MA	MA-SEF	Sefrou
\N	1146	6027	MA	MA-SET	Settat
\N	1146	6028	MA	MA-SIK	Sidl Kacem
\N	1146	6029	MA	MA-TNG	Tanger
\N	1146	6030	MA	MA-TNT	Tan-Tan
\N	1146	6031	MA	MA-TAO	Taounate
\N	1146	6032	MA	MA-TAR	Taroudannt
\N	1146	6033	MA	MA-TAT	Tata
\N	1146	6034	MA	MA-TAZ	Taza
\N	1146	6035	MA	MA-TET	Tétouan
\N	1146	6036	MA	MA-TIZ	Tiznit
\N	1142	6037	MD	MD-GA	Gagauzia, Unitate Teritoriala Autonoma
\N	1142	6038	MD	MD-CU	Chisinau
\N	1142	6039	MD	MD-SN	Stinga Nistrului, unitatea teritoriala din
\N	1142	6040	MD	MD-BA	Balti
\N	1142	6041	MD	MD-CA	Cahul
\N	1142	6042	MD	MD-ED	Edinet
\N	1142	6043	MD	MD-LA	Lapusna
\N	1142	6044	MD	MD-OR	Orhei
\N	1142	6045	MD	MD-SO	Soroca
\N	1142	6046	MD	MD-TA	Taraclia
\N	1142	6047	MD	MD-TI	Tighina [Bender]
\N	1142	6048	MD	MD-UN	Ungheni
\N	1129	6049	MG	MG-T	Antananarivo
\N	1129	6050	MG	MG-D	Antsiranana
\N	1129	6051	MG	MG-F	Fianarantsoa
\N	1129	6052	MG	MG-M	Mahajanga
\N	1129	6053	MG	MG-A	Toamasina
\N	1129	6054	MG	MG-U	Toliara
\N	1135	6055	MH	MH-ALL	Ailinglapalap
\N	1135	6056	MH	MH-ALK	Ailuk
\N	1135	6057	MH	MH-ARN	Arno
\N	1135	6058	MH	MH-AUR	Aur
\N	1135	6059	MH	MH-EBO	Ebon
\N	1135	6060	MH	MH-ENI	Eniwetok
\N	1135	6061	MH	MH-JAL	Jaluit
\N	1135	6062	MH	MH-KIL	Kili
\N	1135	6063	MH	MH-KWA	Kwajalein
\N	1135	6064	MH	MH-LAE	Lae
\N	1135	6065	MH	MH-LIB	Lib
\N	1135	6066	MH	MH-LIK	Likiep
\N	1135	6067	MH	MH-MAJ	Majuro
\N	1135	6068	MH	MH-MAL	Maloelap
\N	1135	6069	MH	MH-MEJ	Mejit
\N	1135	6070	MH	MH-MIL	Mili
\N	1135	6071	MH	MH-NMK	Namorik
\N	1135	6072	MH	MH-NMU	Namu
\N	1135	6073	MH	MH-RON	Rongelap
\N	1135	6074	MH	MH-UJA	Ujae
\N	1135	6075	MH	MH-UJL	Ujelang
\N	1135	6076	MH	MH-UTI	Utirik
\N	1135	6077	MH	MH-WTN	Wotho
\N	1135	6078	MH	MH-WTJ	Wotje
\N	1133	6079	ML	ML-BK0	Bamako
\N	1133	6080	ML	ML-7	Gao
\N	1133	6081	ML	ML-1	Kayes
\N	1133	6082	ML	ML-8	Kidal
\N	1133	6083	ML	ML-2	Xoulikoro
\N	1133	6084	ML	ML-5	Mopti
\N	1133	6085	ML	ML-4	S69ou
\N	1133	6086	ML	ML-3	Sikasso
\N	1133	6087	ML	ML-6	Tombouctou
\N	1035	6088	MM	MM-07	Ayeyarwady
\N	1035	6089	MM	MM-02	Bago
\N	1035	6090	MM	MM-03	Magway
\N	1035	6091	MM	MM-04	Mandalay
\N	1035	6092	MM	MM-01	Sagaing
\N	1035	6093	MM	MM-05	Tanintharyi
\N	1035	6094	MM	MM-06	Yangon
\N	1035	6095	MM	MM-14	Chin
\N	1035	6096	MM	MM-11	Kachin
\N	1035	6097	MM	MM-12	Kayah
\N	1035	6098	MM	MM-13	Kayin
\N	1035	6099	MM	MM-15	Mon
\N	1035	6100	MM	MM-16	Rakhine
\N	1035	6101	MM	MM-17	Shan
\N	1144	6102	MN	MN-1	Ulanbaatar
\N	1144	6103	MN	MN-073	Arhangay
\N	1144	6104	MN	MN-069	Bayanhongor
\N	1144	6105	MN	MN-071	Bayan-Olgiy
\N	1144	6106	MN	MN-067	Bulgan
\N	1144	6107	MN	MN-037	Darhan uul
\N	1144	6108	MN	MN-061	Dornod
\N	1144	6109	MN	MN-063	Dornogov,
\N	1144	6110	MN	MN-059	DundgovL
\N	1144	6111	MN	MN-057	Dzavhan
\N	1144	6112	MN	MN-065	Govi-Altay
\N	1144	6113	MN	MN-064	Govi-Smber
\N	1144	6114	MN	MN-039	Hentiy
\N	1144	6115	MN	MN-043	Hovd
\N	1144	6116	MN	MN-041	Hovsgol
\N	1144	6117	MN	MN-053	Omnogovi
\N	1144	6118	MN	MN-035	Orhon
\N	1144	6119	MN	MN-055	Ovorhangay
\N	1144	6120	MN	MN-049	Selenge
\N	1144	6121	MN	MN-051	Shbaatar
\N	1144	6122	MN	MN-047	Tov
\N	1144	6123	MN	MN-046	Uvs
\N	1137	6124	MR	MR-NKC	Nouakchott
\N	1137	6125	MR	MR-03	Assaba
\N	1137	6126	MR	MR-05	Brakna
\N	1137	6127	MR	MR-08	Dakhlet Nouadhibou
\N	1137	6128	MR	MR-04	Gorgol
\N	1137	6129	MR	MR-10	Guidimaka
\N	1137	6130	MR	MR-01	Hodh ech Chargui
\N	1137	6131	MR	MR-02	Hodh el Charbi
\N	1137	6132	MR	MR-12	Inchiri
\N	1137	6133	MR	MR-09	Tagant
\N	1137	6134	MR	MR-11	Tiris Zemmour
\N	1137	6135	MR	MR-06	Trarza
\N	1138	6136	MU	MU-BR	Beau Bassin-Rose Hill
\N	1138	6137	MU	MU-CU	Curepipe
\N	1138	6138	MU	MU-PU	Port Louis
\N	1138	6139	MU	MU-QB	Quatre Bornes
\N	1138	6140	MU	MU-VP	Vacosa-Phoenix
\N	1138	6141	MU	MU-BL	Black River
\N	1138	6142	MU	MU-FL	Flacq
\N	1138	6143	MU	MU-GP	Grand Port
\N	1138	6144	MU	MU-MO	Moka
\N	1138	6145	MU	MU-PA	Pamplemousses
\N	1138	6146	MU	MU-PW	Plaines Wilhems
\N	1138	6147	MU	MU-RP	Riviere du Rempart
\N	1138	6148	MU	MU-SA	Savanne
\N	1138	6149	MU	MU-AG	Agalega Islands
\N	1138	6150	MU	MU-CC	Cargados Carajos Shoals
\N	1138	6151	MU	MU-RO	Rodrigues Island
\N	1132	6152	MV	MV-MLE	Male
\N	1132	6153	MV	MV-02	Alif
\N	1132	6154	MV	MV-20	Baa
\N	1132	6155	MV	MV-17	Dhaalu
\N	1132	6156	MV	MV-14	Faafu
\N	1132	6157	MV	MV-27	Gaaf Alif
\N	1132	6158	MV	MV-28	Gaefu Dhaalu
\N	1132	6159	MV	MV-29	Gnaviyani
\N	1132	6160	MV	MV-07	Haa Alif
\N	1132	6161	MV	MV-23	Haa Dhaalu
\N	1132	6162	MV	MV-26	Kaafu
\N	1132	6163	MV	MV-05	Laamu
\N	1132	6164	MV	MV-03	Lhaviyani
\N	1132	6165	MV	MV-12	Meemu
\N	1132	6166	MV	MV-25	Noonu
\N	1132	6167	MV	MV-13	Raa
\N	1132	6168	MV	MV-01	Seenu
\N	1132	6169	MV	MV-24	Shaviyani
\N	1132	6170	MV	MV-08	Thaa
\N	1132	6171	MV	MV-04	Vaavu
\N	1130	6172	MW	MW-BA	Balaka
\N	1130	6173	MW	MW-BL	Blantyre
\N	1130	6174	MW	MW-CK	Chikwawa
\N	1130	6175	MW	MW-CR	Chiradzulu
\N	1130	6176	MW	MW-CT	Chitipa
\N	1130	6177	MW	MW-DE	Dedza
\N	1130	6178	MW	MW-DO	Dowa
\N	1130	6179	MW	MW-KR	Karonga
\N	1130	6180	MW	MW-KS	Kasungu
\N	1130	6181	MW	MW-LK	Likoma Island
\N	1130	6182	MW	MW-LI	Lilongwe
\N	1130	6183	MW	MW-MH	Machinga
\N	1130	6184	MW	MW-MG	Mangochi
\N	1130	6185	MW	MW-MC	Mchinji
\N	1130	6186	MW	MW-MU	Mulanje
\N	1130	6187	MW	MW-MW	Mwanza
\N	1130	6188	MW	MW-MZ	Mzimba
\N	1130	6189	MW	MW-NB	Nkhata Bay
\N	1130	6190	MW	MW-NK	Nkhotakota
\N	1130	6191	MW	MW-NS	Nsanje
\N	1130	6192	MW	MW-NU	Ntcheu
\N	1130	6193	MW	MW-NI	Ntchisi
\N	1130	6194	MW	MW-PH	Phalomba
\N	1130	6195	MW	MW-RU	Rumphi
\N	1130	6196	MW	MW-SA	Salima
\N	1130	6197	MW	MW-TH	Thyolo
\N	1130	6198	MW	MW-ZO	Zomba
\N	1140	6199	MX	MX-AGU	Aguascalientes
\N	1140	6200	MX	MX-BCN	Baja California
\N	1140	6201	MX	MX-BCS	Baja California Sur
\N	1140	6202	MX	MX-CAM	Campeche
\N	1140	6203	MX	MX-COA	Coahu ila
\N	1140	6204	MX	MX-COL	Col ima
\N	1140	6205	MX	MX-CHP	Chiapas
\N	1140	6206	MX	MX-CHH	Chihushua
\N	1140	6207	MX	MX-DUR	Durango
\N	1140	6208	MX	MX-GUA	Guanajuato
\N	1140	6209	MX	MX-GRO	Guerrero
\N	1140	6210	MX	MX-HID	Hidalgo
\N	1140	6211	MX	MX-JAL	Jalisco
\N	1140	6212	MX	MX-MEX	Mexico
\N	1140	6213	MX	MX-MIC	Michoacin
\N	1140	6214	MX	MX-MOR	Moreloa
\N	1140	6215	MX	MX-NAY	Nayarit
\N	1140	6216	MX	MX-NLE	Nuevo Leon
\N	1140	6217	MX	MX-OAX	Oaxaca
\N	1140	6218	MX	MX-PUE	Puebla
\N	1140	6219	MX	MX-QUE	Queretaro
\N	1140	6220	MX	MX-ROO	Quintana Roo
\N	1140	6221	MX	MX-SLP	San Luis Potosi
\N	1140	6222	MX	MX-SIN	Sinaloa
\N	1140	6223	MX	MX-SON	Sonora
\N	1140	6224	MX	MX-TAB	Tabasco
\N	1140	6225	MX	MX-TAM	Tamaulipas
\N	1140	6226	MX	MX-TLA	Tlaxcala
\N	1140	6227	MX	MX-VER	Veracruz
\N	1140	6228	MX	MX-YUC	Yucatan
\N	1140	6229	MX	MX-ZAC	Zacatecas
\N	1131	6230	MY	MY-14	Wilayah Persekutuan Kuala Lumpur
\N	1131	6231	MY	MY-15	Wilayah Persekutuan Labuan
\N	1131	6232	MY	MY-16	Wilayah Persekutuan Putrajaya
\N	1131	6233	MY	MY-01	Johor
\N	1131	6234	MY	MY-02	Kedah
\N	1131	6235	MY	MY-03	Kelantan
\N	1131	6236	MY	MY-04	Melaka
\N	1131	6237	MY	MY-05	Negeri Sembilan
\N	1131	6238	MY	MY-06	Pahang
\N	1131	6239	MY	MY-08	Perak
\N	1131	6240	MY	MY-09	Perlis
\N	1131	6241	MY	MY-07	Pulau Pinang
\N	1131	6242	MY	MY-12	Sabah
\N	1131	6243	MY	MY-13	Sarawak
\N	1131	6244	MY	MY-10	Selangor
\N	1131	6245	MY	MY-11	Terengganu
\N	1147	6246	MZ	MZ-MPM	Maputo
\N	1147	6247	MZ	MZ-P	Cabo Delgado
\N	1147	6248	MZ	MZ-G	Gaza
\N	1147	6249	MZ	MZ-I	Inhambane
\N	1147	6250	MZ	MZ-B	Manica
\N	1147	6251	MZ	MZ-N	Numpula
\N	1147	6252	MZ	MZ-A	Niaaea
\N	1147	6253	MZ	MZ-S	Sofala
\N	1147	6254	MZ	MZ-T	Tete
\N	1147	6255	MZ	MZ-Q	Zambezia
\N	1148	6256	NA	NA-CA	Caprivi
\N	1148	6257	NA	NA-ER	Erongo
\N	1148	6258	NA	NA-HA	Hardap
\N	1148	6259	NA	NA-KA	Karas
\N	1148	6260	NA	NA-KH	Khomae
\N	1148	6261	NA	NA-KU	Kunene
\N	1148	6262	NA	NA-OW	Ohangwena
\N	1148	6263	NA	NA-OK	Okavango
\N	1148	6264	NA	NA-OH	Omaheke
\N	1148	6265	NA	NA-OS	Omusati
\N	1148	6266	NA	NA-ON	Oshana
\N	1148	6267	NA	NA-OT	Oshikoto
\N	1148	6268	NA	NA-OD	Otjozondjupa
\N	1156	6269	NE	NE-8	Niamey
\N	1156	6270	NE	NE-1	Agadez
\N	1156	6271	NE	NE-2	Diffa
\N	1156	6272	NE	NE-3	Dosso
\N	1156	6273	NE	NE-4	Maradi
\N	1156	6274	NE	NE-S	Tahoua
\N	1156	6275	NE	NE-6	Tillaberi
\N	1156	6276	NE	NE-7	Zinder
\N	1157	6277	NG	NG-FC	Abuja Capital Territory
\N	1157	6278	NG	NG-AB	Abia
\N	1157	6279	NG	NG-AD	Adamawa
\N	1157	6280	NG	NG-AK	Akwa Ibom
\N	1157	6281	NG	NG-AN	Anambra
\N	1157	6282	NG	NG-BA	Bauchi
\N	1157	6283	NG	NG-BY	Bayelsa
\N	1157	6284	NG	NG-BE	Benue
\N	1157	6285	NG	NG-BO	Borno
\N	1157	6286	NG	NG-CR	Cross River
\N	1157	6287	NG	NG-DE	Delta
\N	1157	6288	NG	NG-EB	Ebonyi
\N	1157	6289	NG	NG-ED	Edo
\N	1157	6290	NG	NG-EK	Ekiti
\N	1157	6291	NG	NG-EN	Enugu
\N	1157	6292	NG	NG-GO	Gombe
\N	1157	6293	NG	NG-IM	Imo
\N	1157	6294	NG	NG-JI	Jigawa
\N	1157	6295	NG	NG-KD	Kaduna
\N	1157	6296	NG	NG-KN	Kano
\N	1157	6297	NG	NG-KT	Katsina
\N	1157	6298	NG	NG-KE	Kebbi
\N	1157	6299	NG	NG-KO	Kogi
\N	1157	6300	NG	NG-KW	Kwara
\N	1157	6301	NG	NG-LA	Lagos
\N	1157	6302	NG	NG-NA	Nassarawa
\N	1157	6303	NG	NG-NI	Niger
\N	1157	6304	NG	NG-OG	Ogun
\N	1157	6305	NG	NG-ON	Ondo
\N	1157	6306	NG	NG-OS	Osun
\N	1157	6307	NG	NG-OY	Oyo
\N	1157	6308	NG	NG-RI	Rivers
\N	1157	6309	NG	NG-SO	Sokoto
\N	1157	6310	NG	NG-TA	Taraba
\N	1157	6311	NG	NG-YO	Yobe
\N	1157	6312	NG	NG-ZA	Zamfara
\N	1155	6313	NI	NI-BO	Boaco
\N	1155	6314	NI	NI-CA	Carazo
\N	1155	6315	NI	NI-CI	Chinandega
\N	1155	6316	NI	NI-CO	Chontales
\N	1155	6317	NI	NI-ES	Esteli
\N	1155	6318	NI	NI-JI	Jinotega
\N	1155	6319	NI	NI-LE	Leon
\N	1155	6320	NI	NI-MD	Madriz
\N	1155	6321	NI	NI-MN	Managua
\N	1155	6322	NI	NI-MS	Masaya
\N	1155	6323	NI	NI-MT	Matagalpa
\N	1155	6324	NI	NI-NS	Nueva Segovia
\N	1155	6325	NI	NI-SJ	Rio San Juan
\N	1155	6326	NI	NI-RI	Rivas
\N	1155	6327	NI	NI-AN	Atlantico Norte
\N	1155	6328	NI	NI-AS	Atlantico Sur
\N	1152	6329	NL	NL-DR	Drente
\N	1152	6330	NL	NL-FL	Flevoland
\N	1152	6331	NL	NL-FR	Friesland
\N	1152	6332	NL	NL-GL	Gelderland
\N	1152	6333	NL	NL-GR	Groningen
\N	1152	6334	NL	NL-NB	Noord-Brabant
\N	1152	6335	NL	NL-NH	Noord-Holland
\N	1152	6336	NL	NL-OV	Overijssel
\N	1152	6337	NL	NL-UT	Utrecht
\N	1152	6338	NL	NL-ZH	Zuid-Holland
\N	1152	6339	NL	NL-ZL	Zeeland
\N	1161	6340	NO	NO-02	Akershus
\N	1161	6341	NO	NO-09	Aust-Agder
\N	1161	6342	NO	NO-06	Buskerud
\N	1161	6343	NO	NO-20	Finumark
\N	1161	6344	NO	NO-04	Hedmark
\N	1161	6345	NO	NO-12	Hordaland
\N	1161	6346	NO	NO-15	Mire og Romsdal
\N	1161	6347	NO	NO-18	Nordland
\N	1161	6348	NO	NO-17	Nord-Trindelag
\N	1161	6349	NO	NO-05	Oppland
\N	1161	6350	NO	NO-03	Oslo
\N	1161	6351	NO	NO-11	Rogaland
\N	1161	6352	NO	NO-14	Sogn og Fjordane
\N	1161	6353	NO	NO-16	Sir-Trindelag
\N	1161	6354	NO	NO-06	Telemark
\N	1161	6355	NO	NO-19	Troms
\N	1161	6356	NO	NO-10	Vest-Agder
\N	1161	6357	NO	NO-07	Vestfold
\N	1161	6358	NO	NO-01	Ostfold
\N	1161	6359	NO	NO-22	Jan Mayen
\N	1161	6360	NO	NO-21	Svalbard
\N	1154	6361	NZ	NZ-AUK	Auckland
\N	1154	6362	NZ	NZ-BOP	Bay of Plenty
\N	1154	6363	NZ	NZ-CAN	Canterbury
\N	1154	6364	NZ	NZ-GIS	Gisborne
\N	1154	6365	NZ	NZ-HKB	Hawkes's Bay
\N	1154	6366	NZ	NZ-MWT	Manawatu-Wanganui
\N	1154	6367	NZ	NZ-MBH	Marlborough
\N	1154	6368	NZ	NZ-NSN	Nelson
\N	1154	6369	NZ	NZ-NTL	Northland
\N	1154	6370	NZ	NZ-OTA	Otago
\N	1154	6371	NZ	NZ-STL	Southland
\N	1154	6372	NZ	NZ-TKI	Taranaki
\N	1154	6373	NZ	NZ-TAS	Tasman
\N	1154	6374	NZ	NZ-WKO	waikato
\N	1154	6375	NZ	NZ-WGN	Wellington
\N	1154	6376	NZ	NZ-WTC	West Coast
\N	1162	6377	OM	OM-DA	Ad Dakhillyah
\N	1162	6378	OM	OM-BA	Al Batinah
\N	1162	6379	OM	OM-JA	Al Janblyah
\N	1162	6380	OM	OM-WU	Al Wusta
\N	1162	6381	OM	OM-SH	Ash Sharqlyah
\N	1162	6382	OM	OM-ZA	Az Zahirah
\N	1162	6383	OM	OM-MA	Masqat
\N	1162	6384	OM	OM-MU	Musandam
\N	1166	6385	PA	PA-1	Bocas del Toro
\N	1166	6386	PA	PA-2	Cocle
\N	1166	6387	PA	PA-4	Chiriqui
\N	1166	6388	PA	PA-5	Darien
\N	1166	6389	PA	PA-6	Herrera
\N	1166	6390	PA	PA-7	Loa Santoa
\N	1166	6391	PA	PA-8	Panama
\N	1166	6392	PA	PA-9	Veraguas
\N	1166	6393	PA	PA-Q	Comarca de San Blas
\N	1169	6394	PE	PE-CAL	El Callao
\N	1169	6395	PE	PE-ANC	Ancash
\N	1169	6396	PE	PE-APU	Apurimac
\N	1169	6397	PE	PE-ARE	Arequipa
\N	1169	6398	PE	PE-AYA	Ayacucho
\N	1169	6399	PE	PE-CAJ	Cajamarca
\N	1169	6400	PE	PE-CUS	Cuzco
\N	1169	6401	PE	PE-HUV	Huancavelica
\N	1169	6402	PE	PE-HUC	Huanuco
\N	1169	6403	PE	PE-ICA	Ica
\N	1169	6404	PE	PE-JUN	Junin
\N	1169	6405	PE	PE-LAL	La Libertad
\N	1169	6406	PE	PE-LAM	Lambayeque
\N	1169	6407	PE	PE-LIM	Lima
\N	1169	6408	PE	PE-LOR	Loreto
\N	1169	6409	PE	PE-MDD	Madre de Dios
\N	1169	6410	PE	PE-MOQ	Moquegua
\N	1169	6411	PE	PE-PAS	Pasco
\N	1169	6412	PE	PE-PIU	Piura
\N	1169	6413	PE	PE-PUN	Puno
\N	1169	6414	PE	PE-SAM	San Martin
\N	1169	6415	PE	PE-TAC	Tacna
\N	1169	6416	PE	PE-TUM	Tumbes
\N	1169	6417	PE	PE-UCA	Ucayali
\N	1167	6418	PG	PG-NCD	National Capital District (Port Moresby)
\N	1167	6419	PG	PG-CPK	Chimbu
\N	1167	6420	PG	PG-EHG	Eastern Highlands
\N	1167	6421	PG	PG-EBR	East New Britain
\N	1167	6422	PG	PG-ESW	East Sepik
\N	1167	6423	PG	PG-EPW	Enga
\N	1167	6424	PG	PG-GPK	Gulf
\N	1167	6425	PG	PG-MPM	Madang
\N	1167	6426	PG	PG-MRL	Manus
\N	1167	6427	PG	PG-MBA	Milne Bay
\N	1167	6428	PG	PG-MPL	Morobe
\N	1167	6429	PG	PG-NIK	New Ireland
\N	1167	6430	PG	PG-NSA	North Solomons
\N	1167	6431	PG	PG-SAN	Santaun
\N	1167	6432	PG	PG-SHM	Southern Highlands
\N	1167	6433	PG	PG-WHM	Western Highlands
\N	1167	6434	PG	PG-WBK	West New Britain
\N	1170	6435	PH	PH-ABR	Abra
\N	1170	6436	PH	PH-AGN	Agusan del Norte
\N	1170	6437	PH	PH-AGS	Agusan del Sur
\N	1170	6438	PH	PH-AKL	Aklan
\N	1170	6439	PH	PH-ALB	Albay
\N	1170	6440	PH	PH-ANT	Antique
\N	1170	6441	PH	PH-APA	Apayao
\N	1170	6442	PH	PH-AUR	Aurora
\N	1170	6443	PH	PH-BAS	Basilan
\N	1170	6444	PH	PH-BAN	Batasn
\N	1170	6445	PH	PH-BTN	Batanes
\N	1170	6446	PH	PH-BTG	Batangas
\N	1170	6447	PH	PH-BEN	Benguet
\N	1170	6448	PH	PH-BIL	Biliran
\N	1170	6449	PH	PH-BOH	Bohol
\N	1170	6450	PH	PH-BUK	Bukidnon
\N	1170	6451	PH	PH-BUL	Bulacan
\N	1170	6452	PH	PH-CAG	Cagayan
\N	1170	6453	PH	PH-CAN	Camarines Norte
\N	1170	6454	PH	PH-CAS	Camarines Sur
\N	1170	6455	PH	PH-CAM	Camiguin
\N	1170	6456	PH	PH-CAP	Capiz
\N	1170	6457	PH	PH-CAT	Catanduanes
\N	1170	6458	PH	PH-CAV	Cavite
\N	1170	6459	PH	PH-CEB	Cebu
\N	1170	6460	PH	PH-COM	Compostela Valley
\N	1170	6461	PH	PH-DAV	Davao
\N	1170	6462	PH	PH-DAS	Davao del Sur
\N	1170	6463	PH	PH-DAO	Davao Oriental
\N	1170	6464	PH	PH-EAS	Eastern Samar
\N	1170	6465	PH	PH-GUI	Guimaras
\N	1170	6466	PH	PH-IFU	Ifugao
\N	1170	6467	PH	PH-ILN	Ilocos Norte
\N	1170	6468	PH	PH-ILS	Ilocos Sur
\N	1170	6469	PH	PH-ILI	Iloilo
\N	1170	6470	PH	PH-ISA	Isabela
\N	1170	6471	PH	PH-KAL	Kalinga-Apayso
\N	1170	6472	PH	PH-LAG	Laguna
\N	1170	6473	PH	PH-LAN	Lanao del Norte
\N	1170	6474	PH	PH-LAS	Lanao del Sur
\N	1170	6475	PH	PH-LUN	La Union
\N	1170	6476	PH	PH-LEY	Leyte
\N	1170	6477	PH	PH-MAG	Maguindanao
\N	1170	6478	PH	PH-MAD	Marinduque
\N	1170	6479	PH	PH-MAS	Masbate
\N	1170	6480	PH	PH-MDC	Mindoro Occidental
\N	1170	6481	PH	PH-MDR	Mindoro Oriental
\N	1170	6482	PH	PH-MSC	Misamis Occidental
\N	1170	6483	PH	PH-MSR	Misamis Oriental
\N	1170	6484	PH	PH-MOU	Mountain Province
\N	1170	6485	PH	PH-NEC	Negroe Occidental
\N	1170	6486	PH	PH-NER	Negros Oriental
\N	1170	6487	PH	PH-NCO	North Cotabato
\N	1170	6488	PH	PH-NSA	Northern Samar
\N	1170	6489	PH	PH-NUE	Nueva Ecija
\N	1170	6490	PH	PH-NUV	Nueva Vizcaya
\N	1170	6491	PH	PH-PLW	Palawan
\N	1170	6492	PH	PH-PAM	Pampanga
\N	1170	6493	PH	PH-PAN	Pangasinan
\N	1170	6494	PH	PH-QUE	Quezon
\N	1170	6495	PH	PH-QUI	Quirino
\N	1170	6496	PH	PH-RIZ	Rizal
\N	1170	6497	PH	PH-ROM	Romblon
\N	1170	6498	PH	PH-SAR	Sarangani
\N	1170	6499	PH	PH-SIG	Siquijor
\N	1170	6500	PH	PH-SOR	Sorsogon
\N	1170	6501	PH	PH-SCO	South Cotabato
\N	1170	6502	PH	PH-SLE	Southern Leyte
\N	1170	6503	PH	PH-SUK	Sultan Kudarat
\N	1170	6504	PH	PH-SLU	Sulu
\N	1170	6505	PH	PH-SUN	Surigao del Norte
\N	1170	6506	PH	PH-SUR	Surigao del Sur
\N	1170	6507	PH	PH-TAR	Tarlac
\N	1170	6508	PH	PH-TAW	Tawi-Tawi
\N	1170	6509	PH	PH-WSA	Western Samar
\N	1170	6510	PH	PH-ZMB	Zambales
\N	1170	6511	PH	PH-ZAN	Zamboanga del Norte
\N	1170	6512	PH	PH-ZAS	Zamboanga del Sur
\N	1170	6513	PH	PH-ZSI	Zamboanga Sibiguey
\N	1163	6514	PK	PK-IS	Islamabad
\N	1163	6515	PK	PK-BA	Baluchistan (en)
\N	1163	6516	PK	PK-NW	North-West Frontier
\N	1163	6517	PK	PK-SD	Sind (en)
\N	1163	6518	PK	PK-TA	Federally Administered Tribal Aresa
\N	1163	6519	PK	PK-JK	Azad Rashmir
\N	1163	6520	PK	PK-NA	Northern Areas
1302	1172	6521	PL	PL-DS	Dolnośląskie
1303	1172	6522	PL	PL-KP	Kujawsko-pomorskie
1304	1172	6523	PL	PL-LU	Lubelskie
1305	1172	6524	PL	PL-LB	Lubuskie
1306	1172	6525	PL	PL-LD	Łódzkie
1307	1172	6526	PL	PL-MA	Małopolskie
1308	1172	6528	PL	PL-OP	Opolskie
1309	1172	6529	PL	PL-PK	Podkarpackie
1310	1172	6530	PL	PL-PD	Podlaskie
1311	1172	6532	PL	PL-SL	Śląskie
1312	1172	6533	PL	PL-SK	Świętokrzyskie
1313	1172	6534	PL	PL-WN	Warmińsko-mazurskie
1314	1172	6535	PL	PL-WP	Wielkopolskie
1315	1172	6536	PL	PL-ZP	Zachodniopomorskie
\N	1173	6537	PT	PT-01	Aveiro
\N	1173	6538	PT	PT-02	Beja
\N	1173	6539	PT	PT-03	Braga
\N	1173	6540	PT	PT-04	Braganca
\N	1173	6541	PT	PT-05	Castelo Branco
\N	1173	6542	PT	PT-06	Colmbra
\N	1173	6543	PT	PT-07	Ovora
\N	1173	6544	PT	PT-08	Faro
\N	1173	6545	PT	PT-09	Guarda
\N	1173	6546	PT	PT-10	Leiria
\N	1173	6547	PT	PT-11	Lisboa
\N	1173	6548	PT	PT-12	Portalegre
\N	1173	6549	PT	PT-13	Porto
\N	1173	6550	PT	PT-14	Santarem
\N	1173	6551	PT	PT-15	Setubal
\N	1173	6552	PT	PT-16	Viana do Castelo
\N	1173	6553	PT	PT-17	Vila Real
\N	1173	6554	PT	PT-18	Viseu
\N	1173	6555	PT	PT-20	Regiao Autonoma dos Acores
\N	1173	6556	PT	PT-30	Regiao Autonoma da Madeira
\N	1168	6557	PY	PY-ASU	Asuncion
\N	1168	6558	PY	PY-16	Alto Paraguay
\N	1168	6559	PY	PY-10	Alto Parana
\N	1168	6560	PY	PY-13	Amambay
\N	1168	6561	PY	PY-19	Boqueron
\N	1168	6562	PY	PY-5	Caeguazu
\N	1168	6563	PY	PY-6	Caazapl
\N	1168	6564	PY	PY-14	Canindeyu
\N	1168	6565	PY	PY-1	Concepcion
\N	1168	6566	PY	PY-3	Cordillera
\N	1168	6567	PY	PY-4	Guaira
\N	1168	6568	PY	PY-7	Itapua
\N	1168	6569	PY	PY-8	Miaiones
\N	1168	6570	PY	PY-12	Neembucu
\N	1168	6571	PY	PY-9	Paraguari
\N	1168	6572	PY	PY-15	Presidente Hayes
\N	1168	6573	PY	PY-2	San Pedro
\N	1175	6574	QA	QA-DA	Ad Dawhah
\N	1175	6575	QA	QA-GH	Al Ghuwayriyah
\N	1175	6576	QA	QA-JU	Al Jumayliyah
\N	1175	6577	QA	QA-KH	Al Khawr
\N	1175	6578	QA	QA-WA	Al Wakrah
\N	1175	6579	QA	QA-RA	Ar Rayyan
\N	1175	6580	QA	QA-JB	Jariyan al Batnah
\N	1175	6581	QA	QA-MS	Madinat ash Shamal
\N	1175	6582	QA	QA-US	Umm Salal
\N	1176	6583	RO	RO-B	Bucuresti
\N	1176	6584	RO	RO-AB	Alba
\N	1176	6585	RO	RO-AR	Arad
\N	1176	6586	RO	RO-AG	Arges
\N	1176	6587	RO	RO-BC	Bacau
\N	1176	6588	RO	RO-BH	Bihor
\N	1176	6589	RO	RO-BN	Bistrita-Nasaud
\N	1176	6590	RO	RO-BT	Boto'ani
\N	1176	6591	RO	RO-BV	Bra'ov
\N	1176	6592	RO	RO-BR	Braila
\N	1176	6593	RO	RO-BZ	Buzau
\N	1176	6594	RO	RO-CS	Caras-Severin
\N	1176	6595	RO	RO-CL	Ca la ras'i
\N	1176	6596	RO	RO-CJ	Cluj
\N	1176	6597	RO	RO-CT	Constant'a
\N	1176	6598	RO	RO-CV	Covasna
\N	1176	6599	RO	RO-DB	Dambovit'a
\N	1176	6600	RO	RO-DJ	Dolj
\N	1176	6601	RO	RO-GL	Galat'i
\N	1176	6602	RO	RO-GR	Giurgiu
\N	1176	6603	RO	RO-GJ	Gorj
\N	1176	6604	RO	RO-HR	Harghita
\N	1176	6605	RO	RO-HD	Hunedoara
\N	1176	6606	RO	RO-IL	Ialomit'a
\N	1176	6607	RO	RO-IS	Ias'i
\N	1176	6608	RO	RO-IF	Ilfov
\N	1176	6609	RO	RO-MM	Maramures
\N	1176	6610	RO	RO-MH	Mehedint'i
\N	1176	6611	RO	RO-MS	Mures
\N	1176	6612	RO	RO-NT	Neamt
\N	1176	6613	RO	RO-OT	Olt
\N	1176	6614	RO	RO-PH	Prahova
\N	1176	6615	RO	RO-SM	Satu Mare
\N	1176	6616	RO	RO-SJ	Sa laj
\N	1176	6617	RO	RO-SB	Sibiu
\N	1176	6618	RO	RO-SV	Suceava
\N	1176	6619	RO	RO-TR	Teleorman
\N	1176	6620	RO	RO-TM	Timis
\N	1176	6621	RO	RO-TL	Tulcea
\N	1176	6622	RO	RO-VS	Vaslui
\N	1176	6623	RO	RO-VL	Valcea
\N	1176	6624	RO	RO-VN	Vrancea
\N	1177	6625	RU	RU-AD	Adygeya, Respublika
\N	1177	6626	RU	RU-AL	Altay, Respublika
\N	1177	6627	RU	RU-BA	Bashkortostan, Respublika
\N	1177	6628	RU	RU-BU	Buryatiya, Respublika
\N	1177	6629	RU	RU-CE	Chechenskaya Respublika
\N	1177	6630	RU	RU-CU	Chuvashskaya Respublika
\N	1177	6631	RU	RU-DA	Dagestan, Respublika
\N	1177	6632	RU	RU-IN	Ingushskaya Respublika
\N	1177	6633	RU	RU-KB	Kabardino-Balkarskaya
\N	1177	6634	RU	RU-KL	Kalmykiya, Respublika
\N	1177	6635	RU	RU-KC	Karachayevo-Cherkesskaya Respublika
\N	1177	6636	RU	RU-KR	Kareliya, Respublika
\N	1177	6637	RU	RU-KK	Khakasiya, Respublika
\N	1177	6638	RU	RU-KO	Komi, Respublika
\N	1177	6639	RU	RU-ME	Mariy El, Respublika
\N	1177	6640	RU	RU-MO	Mordoviya, Respublika
\N	1177	6641	RU	RU-SA	Sakha, Respublika [Yakutiya]
\N	1177	6642	RU	RU-SE	Severnaya Osetiya, Respublika
\N	1177	6643	RU	RU-TA	Tatarstan, Respublika
\N	1177	6644	RU	RU-TY	Tyva, Respublika [Tuva]
\N	1177	6645	RU	RU-UD	Udmurtskaya Respublika
\N	1177	6646	RU	RU-ALT	Altayskiy kray
\N	1177	6647	RU	RU-KHA	Khabarovskiy kray
\N	1177	6648	RU	RU-KDA	Krasnodarskiy kray
\N	1177	6649	RU	RU-KYA	Krasnoyarskiy kray
\N	1177	6650	RU	RU-PRI	Primorskiy kray
\N	1177	6651	RU	RU-STA	Stavropol'skiy kray
\N	1177	6652	RU	RU-AMU	Amurskaya oblast'
\N	1177	6653	RU	RU-ARK	Arkhangel'skaya oblast'
\N	1177	6654	RU	RU-AST	Astrakhanskaya oblast'
\N	1177	6655	RU	RU-BEL	Belgorodskaya oblast'
\N	1177	6656	RU	RU-BRY	Bryanskaya oblast'
\N	1177	6657	RU	RU-CHE	Chelyabinskaya oblast'
\N	1177	6658	RU	RU-CHI	Chitinskaya oblast'
\N	1177	6659	RU	RU-IRK	Irkutskaya oblast'
\N	1177	6660	RU	RU-IVA	Ivanovskaya oblast'
\N	1177	6661	RU	RU-KGD	Kaliningradskaya oblast'
\N	1177	6662	RU	RU-KLU	Kaluzhskaya oblast'
\N	1177	6663	RU	RU-KAM	Kamchatskaya oblast'
\N	1177	6664	RU	RU-KEM	Kemerovskaya oblast'
\N	1177	6665	RU	RU-KIR	Kirovskaya oblast'
\N	1177	6666	RU	RU-KOS	Kostromskaya oblast'
\N	1177	6667	RU	RU-KGN	Kurganskaya oblast'
\N	1177	6668	RU	RU-KRS	Kurskaya oblast'
\N	1177	6669	RU	RU-LEN	Leningradskaya oblast'
\N	1177	6670	RU	RU-LIP	Lipetskaya oblast'
\N	1177	6671	RU	RU-MAG	Magadanskaya oblast'
\N	1177	6672	RU	RU-MOS	Moskovskaya oblast'
\N	1177	6673	RU	RU-MUR	Murmanskaya oblast'
\N	1177	6674	RU	RU-NIZ	Nizhegorodskaya oblast'
\N	1177	6675	RU	RU-NGR	Novgorodskaya oblast'
\N	1177	6676	RU	RU-NVS	Novosibirskaya oblast'
\N	1177	6677	RU	RU-OMS	Omskaya oblast'
\N	1177	6678	RU	RU-ORE	Orenburgskaya oblast'
\N	1177	6679	RU	RU-ORL	Orlovskaya oblast'
\N	1177	6680	RU	RU-PNZ	Penzenskaya oblast'
\N	1177	6681	RU	RU-PER	Permskaya oblast'
\N	1177	6682	RU	RU-PSK	Pskovskaya oblast'
\N	1177	6683	RU	RU-ROS	Rostovskaya oblast'
\N	1177	6684	RU	RU-RYA	Ryazanskaya oblast'
\N	1177	6685	RU	RU-SAK	Sakhalinskaya oblast'
\N	1177	6686	RU	RU-SAM	Samarskaya oblast'
\N	1177	6687	RU	RU-SAR	Saratovskaya oblast'
\N	1177	6688	RU	RU-SMO	Smolenskaya oblast'
\N	1177	6689	RU	RU-SVE	Sverdlovskaya oblast'
\N	1177	6690	RU	RU-TAM	Tambovskaya oblast'
\N	1177	6691	RU	RU-TOM	Tomskaya oblast'
\N	1177	6692	RU	RU-TUL	Tul'skaya oblast'
\N	1177	6693	RU	RU-TVE	Tverskaya oblast'
\N	1177	6694	RU	RU-TYU	Tyumenskaya oblast'
\N	1177	6695	RU	RU-ULY	Ul'yanovskaya oblast'
\N	1177	6696	RU	RU-VLA	Vladimirskaya oblast'
\N	1177	6697	RU	RU-VGG	Volgogradskaya oblast'
\N	1177	6698	RU	RU-VLG	Vologodskaya oblast'
\N	1177	6699	RU	RU-VOR	Voronezhskaya oblast'
\N	1177	6700	RU	RU-YAR	Yaroslavskaya oblast'
\N	1177	6701	RU	RU-MOW	Moskva
\N	1177	6702	RU	RU-SPE	Sankt-Peterburg
\N	1177	6703	RU	RU-YEV	Yevreyskaya avtonomnaya oblast'
\N	1177	6704	RU	RU-AGB	Aginskiy Buryatskiy avtonomnyy
\N	1177	6705	RU	RU-CHU	Chukotskiy avtonomnyy okrug
\N	1177	6706	RU	RU-EVE	Evenkiyskiy avtonomnyy okrug
\N	1177	6707	RU	RU-KHM	Khanty-Mansiyskiy avtonomnyy okrug
\N	1177	6708	RU	RU-KOP	Komi-Permyatskiy avtonomnyy okrug
\N	1177	6709	RU	RU-KOR	Koryakskiy avtonomnyy okrug
\N	1177	6710	RU	RU-NEN	Nenetskiy avtonomnyy okrug
\N	1177	6711	RU	RU-TAY	Taymyrskiy (Dolgano-Nenetskiy)
\N	1177	6712	RU	RU-UOB	Ust'-Ordynskiy Buryatskiy
\N	1177	6713	RU	RU-YAN	Yamalo-Nenetskiy avtonomnyy okrug
\N	1178	6714	RW	RW-C	Butare
\N	1178	6715	RW	RW-I	Byumba
\N	1178	6716	RW	RW-E	Cyangugu
\N	1178	6717	RW	RW-D	Gikongoro
\N	1178	6718	RW	RW-G	Gisenyi
\N	1178	6719	RW	RW-B	Gitarama
\N	1178	6720	RW	RW-J	Kibungo
\N	1178	6721	RW	RW-F	Kibuye
\N	1178	6722	RW	RW-K	Kigali-Rural Kigali y' Icyaro
\N	1178	6723	RW	RW-L	Kigali-Ville Kigali Ngari
\N	1178	6724	RW	RW-M	Mutara
\N	1178	6725	RW	RW-H	Ruhengeri
\N	1187	6726	SA	SA-11	Al Batah
\N	1187	6727	SA	SA-08	Al H,udd ash Shamallyah
\N	1187	6728	SA	SA-12	Al Jawf
\N	1187	6729	SA	SA-03	Al Madinah
\N	1187	6730	SA	SA-05	Al Qasim
\N	1187	6731	SA	SA-01	Ar Riyad
\N	1187	6732	SA	SA-14	Asir
\N	1187	6733	SA	SA-06	Ha'il
\N	1187	6734	SA	SA-09	Jlzan
\N	1187	6735	SA	SA-02	Makkah
\N	1187	6736	SA	SA-10	Najran
\N	1187	6737	SA	SA-07	Tabuk
\N	1194	6738	SB	SB-CT	Capital Territory (Honiara)
\N	1194	6739	SB	SB-GU	Guadalcanal
\N	1194	6740	SB	SB-IS	Isabel
\N	1194	6741	SB	SB-MK	Makira
\N	1194	6742	SB	SB-ML	Malaita
\N	1194	6743	SB	SB-TE	Temotu
\N	1200	6744	SD	SD-23	A'ali an Nil
\N	1200	6745	SD	SD-26	Al Bah al Ahmar
\N	1200	6746	SD	SD-18	Al Buhayrat
\N	1200	6747	SD	SD-07	Al Jazirah
\N	1200	6748	SD	SD-03	Al Khartum
\N	1200	6749	SD	SD-06	Al Qadarif
\N	1200	6750	SD	SD-22	Al Wahdah
\N	1200	6751	SD	SD-04	An Nil
\N	1200	6752	SD	SD-08	An Nil al Abyaq
\N	1200	6753	SD	SD-24	An Nil al Azraq
\N	1200	6754	SD	SD-01	Ash Shamallyah
\N	1200	6755	SD	SD-17	Bahr al Jabal
\N	1200	6756	SD	SD-16	Gharb al Istiwa'iyah
\N	1200	6757	SD	SD-14	Gharb Ba~r al Ghazal
\N	1200	6758	SD	SD-12	Gharb Darfur
\N	1200	6759	SD	SD-10	Gharb Kurdufan
\N	1200	6760	SD	SD-11	Janub Darfur
\N	1200	6761	SD	SD-13	Janub Rurdufan
\N	1200	6762	SD	SD-20	Jnqall
\N	1200	6763	SD	SD-05	Kassala
\N	1200	6764	SD	SD-15	Shamal Batr al Ghazal
\N	1200	6765	SD	SD-02	Shamal Darfur
\N	1200	6766	SD	SD-09	Shamal Kurdufan
\N	1200	6767	SD	SD-19	Sharq al Istiwa'iyah
\N	1200	6768	SD	SD-25	Sinnar
\N	1200	6769	SD	SD-21	Warab
\N	1204	6770	SE	SE-K	Blekinge lan
\N	1204	6771	SE	SE-W	Dalarnas lan
\N	1204	6772	SE	SE-I	Gotlands lan
\N	1204	6773	SE	SE-X	Gavleborge lan
\N	1204	6774	SE	SE-N	Hallands lan
\N	1204	6775	SE	SE-Z	Jamtlande lan
\N	1204	6776	SE	SE-F	Jonkopings lan
\N	1204	6777	SE	SE-H	Kalmar lan
\N	1204	6778	SE	SE-G	Kronoberge lan
\N	1204	6779	SE	SE-BD	Norrbottena lan
\N	1204	6780	SE	SE-M	Skane lan
\N	1204	6781	SE	SE-AB	Stockholms lan
\N	1204	6782	SE	SE-D	Sodermanlands lan
\N	1204	6783	SE	SE-C	Uppsala lan
\N	1204	6784	SE	SE-S	Varmlanda lan
\N	1204	6785	SE	SE-AC	Vasterbottens lan
\N	1204	6786	SE	SE-Y	Vasternorrlands lan
\N	1204	6787	SE	SE-U	Vastmanlanda lan
\N	1204	6788	SE	SE-Q	Vastra Gotalands lan
\N	1204	6789	SE	SE-T	Orebro lan
\N	1204	6790	SE	SE-E	Ostergotlands lan
\N	1180	6791	SH	SH-SH	Saint Helena
\N	1180	6792	SH	SH-AC	Ascension
\N	1180	6793	SH	SH-TA	Tristan da Cunha
\N	1193	6794	SI	SI-001	Ajdovscina
\N	1193	6795	SI	SI-002	Beltinci
\N	1193	6796	SI	SI-148	Benedikt
\N	1193	6797	SI	SI-149	Bistrica ob Sotli
\N	1193	6798	SI	SI-003	Bled
\N	1193	6799	SI	SI-150	Bloke
\N	1193	6800	SI	SI-004	Bohinj
\N	1193	6801	SI	SI-005	Borovnica
\N	1193	6802	SI	SI-006	Bovec
\N	1193	6803	SI	SI-151	Braslovce
\N	1193	6804	SI	SI-007	Brda
\N	1193	6805	SI	SI-008	Brezovica
\N	1193	6806	SI	SI-009	Brezica
\N	1193	6807	SI	SI-152	Cankova
\N	1193	6808	SI	SI-011	Celje
\N	1193	6809	SI	SI-012	Cerklje na Gorenjskem
\N	1193	6810	SI	SI-013	Cerknica
\N	1193	6811	SI	SI-014	Cerkno
\N	1193	6812	SI	SI-153	Cerkvenjak
\N	1193	6813	SI	SI-015	Crensovci
\N	1193	6814	SI	SI-016	Crna na Koroskem
\N	1193	6815	SI	SI-017	Crnomelj
\N	1193	6816	SI	SI-018	Destrnik
\N	1193	6817	SI	SI-019	Divaca
\N	1193	6818	SI	SI-154	Dobje
\N	1193	6819	SI	SI-020	Dobrepolje
\N	1193	6820	SI	SI-155	Dobrna
\N	1193	6821	SI	SI-021	Dobrova-Polhov Gradec
\N	1193	6822	SI	SI-156	Dobrovnik
\N	1193	6823	SI	SI-022	Dol pri Ljubljani
\N	1193	6824	SI	SI-157	Dolenjske Toplice
\N	1193	6825	SI	SI-023	Domzale
\N	1193	6826	SI	SI-024	Dornava
\N	1193	6827	SI	SI-025	Dravograd
\N	1193	6828	SI	SI-026	Duplek
\N	1193	6829	SI	SI-027	Gorenja vas-Poljane
\N	1193	6830	SI	SI-028	Gorsnica
\N	1193	6831	SI	SI-029	Gornja Radgona
\N	1193	6832	SI	SI-030	Gornji Grad
\N	1193	6833	SI	SI-031	Gornji Petrovci
\N	1193	6834	SI	SI-158	Grad
\N	1193	6835	SI	SI-032	Grosuplje
\N	1193	6836	SI	SI-159	Hajdina
\N	1193	6837	SI	SI-160	Hoce-Slivnica
\N	1193	6838	SI	SI-161	Hodos
\N	1193	6839	SI	SI-162	Jorjul
\N	1193	6840	SI	SI-034	Hrastnik
\N	1193	6841	SI	SI-035	Hrpelje-Kozina
\N	1193	6842	SI	SI-036	Idrija
\N	1193	6843	SI	SI-037	Ig
\N	1193	6844	SI	SI-038	IIrska Bistrica
\N	1193	6845	SI	SI-039	Ivancna Gorica
\N	1193	6846	SI	SI-040	Izola
\N	1193	6847	SI	SI-041	Jesenice
\N	1193	6848	SI	SI-163	Jezersko
\N	1193	6849	SI	SI-042	Jursinci
\N	1193	6850	SI	SI-043	Kamnik
\N	1193	6851	SI	SI-044	Kanal
\N	1193	6852	SI	SI-045	Kidricevo
\N	1193	6853	SI	SI-046	Kobarid
\N	1193	6854	SI	SI-047	Kobilje
\N	1193	6855	SI	SI-048	Jovevje
\N	1193	6856	SI	SI-049	Komen
\N	1193	6857	SI	SI-164	Komenda
\N	1193	6858	SI	SI-050	Koper
\N	1193	6859	SI	SI-165	Kostel
\N	1193	6860	SI	SI-051	Kozje
\N	1193	6861	SI	SI-052	Kranj
\N	1193	6862	SI	SI-053	Kranjska Gora
\N	1193	6863	SI	SI-166	Krizevci
\N	1193	6864	SI	SI-054	Krsko
\N	1193	6865	SI	SI-055	Kungota
\N	1193	6866	SI	SI-056	Kuzma
\N	1193	6867	SI	SI-057	Lasko
\N	1193	6868	SI	SI-058	Lenart
\N	1193	6869	SI	SI-059	Lendava
\N	1193	6870	SI	SI-060	Litija
\N	1193	6871	SI	SI-061	Ljubljana
\N	1193	6872	SI	SI-062	Ljubno
\N	1193	6873	SI	SI-063	Ljutomer
\N	1193	6874	SI	SI-064	Logatec
\N	1193	6875	SI	SI-065	Loska dolina
\N	1193	6876	SI	SI-066	Loski Potok
\N	1193	6877	SI	SI-167	Lovrenc na Pohorju
\N	1193	6878	SI	SI-067	Luce
\N	1193	6879	SI	SI-068	Lukovica
\N	1193	6880	SI	SI-069	Majsperk
\N	1193	6881	SI	SI-070	Maribor
\N	1193	6882	SI	SI-168	Markovci
\N	1193	6883	SI	SI-071	Medvode
\N	1193	6884	SI	SI-072	Menges
\N	1193	6885	SI	SI-073	Metlika
\N	1193	6886	SI	SI-074	Mezica
\N	1193	6887	SI	SI-169	Miklavz na Dravskern polju
\N	1193	6888	SI	SI-075	Miren-Kostanjevica
\N	1193	6889	SI	SI-170	Mirna Pec
\N	1193	6890	SI	SI-076	Mislinja
\N	1193	6891	SI	SI-077	Moravce
\N	1193	6892	SI	SI-078	Moravske Toplice
\N	1193	6893	SI	SI-079	Mozirje
\N	1193	6894	SI	SI-080	Murska Sobota
\N	1193	6895	SI	SI-081	Muta
\N	1193	6896	SI	SI-082	Naklo
\N	1193	6897	SI	SI-083	Nazarje
\N	1193	6898	SI	SI-084	Nova Gorica
\N	1193	6899	SI	SI-085	Nova mesto
\N	1193	6900	SI	SI-181	Sveta Ana
\N	1193	6901	SI	SI-182	Sveti Andraz v Slovenskih goricah
\N	1193	6902	SI	SI-116	Sveti Jurij
\N	1193	6903	SI	SI-033	Salovci
\N	1193	6904	SI	SI-183	Sempeter-Vrtojba
\N	1193	6905	SI	SI-117	Sencur
\N	1193	6906	SI	SI-118	Sentilj
\N	1193	6907	SI	SI-119	Sentjernej
\N	1193	6908	SI	SI-120	Sentjur pri Celju
\N	1193	6909	SI	SI-121	Skocjan
\N	1193	6910	SI	SI-122	Skofja Loka
\N	1193	6911	SI	SI-123	Skoftjica
\N	1193	6912	SI	SI-124	Smarje pri Jelsah
\N	1193	6913	SI	SI-125	Smartno ob Paki
\N	1193	6914	SI	SI-194	Smartno pri Litiji
\N	1193	6915	SI	SI-126	Sostanj
\N	1193	6916	SI	SI-127	Store
\N	1193	6917	SI	SI-184	Tabor
\N	1193	6918	SI	SI-010	Tisina
\N	1193	6919	SI	SI-128	Tolmin
\N	1193	6920	SI	SI-129	Trbovje
\N	1193	6921	SI	SI-130	Trebnje
\N	1193	6922	SI	SI-185	Trnovska vas
\N	1193	6923	SI	SI-131	Trzic
\N	1193	6924	SI	SI-186	Trzin
\N	1193	6925	SI	SI-132	Turnisce
\N	1193	6926	SI	SI-133	Velenje
\N	1193	6927	SI	SI-187	Velika Polana
\N	1193	6928	SI	SI-134	Velika Lasce
\N	1193	6929	SI	SI-188	Verzej
\N	1193	6930	SI	SI-135	Videm
\N	1193	6931	SI	SI-136	Vipava
\N	1193	6932	SI	SI-137	Vitanje
\N	1193	6933	SI	SI-138	Vojnik
\N	1193	6934	SI	SI-189	Vransko
\N	1193	6935	SI	SI-140	Vrhnika
\N	1193	6936	SI	SI-141	Vuzenica
\N	1193	6937	SI	SI-142	Zagorje ob Savi
\N	1193	6938	SI	SI-143	Zavrc
\N	1193	6939	SI	SI-144	Zrece
\N	1193	6940	SI	SI-190	Zalec
\N	1193	6941	SI	SI-146	Zelezniki
\N	1193	6942	SI	SI-191	Zetale
\N	1193	6943	SI	SI-147	Ziri
\N	1193	6944	SI	SI-192	Zirovnica
\N	1193	6945	SI	SI-193	Zuzemberk
\N	1192	6946	SK	SK-BC	Banskobystrický kraj
\N	1192	6947	SK	SK-BL	Bratislavský kraj
\N	1192	6948	SK	SK-KI	Košický kraj
\N	1192	6949	SK	SK-NJ	Nitriansky kraj
\N	1192	6950	SK	SK-PV	Prešovský kraj
\N	1192	6951	SK	SK-TC	Trenčiansky kraj
\N	1192	6952	SK	SK-TA	Trnavský kraj
\N	1192	6953	SK	SK-ZI	Žilinský kraj
\N	1190	6954	SL	SL-W	Western Area (Freetown)
\N	1188	6955	SN	SN-DK	Dakar
\N	1188	6956	SN	SN-DB	Diourbel
\N	1188	6957	SN	SN-FK	Fatick
\N	1188	6958	SN	SN-KL	Kaolack
\N	1188	6959	SN	SN-KD	Kolda
\N	1188	6960	SN	SN-LG	Louga
\N	1188	6961	SN	SN-MT	Matam
\N	1188	6962	SN	SN-SL	Saint-Louis
\N	1188	6963	SN	SN-TC	Tambacounda
\N	1188	6964	SN	SN-TH	Thies
\N	1188	6965	SN	SN-ZG	Ziguinchor
\N	1195	6966	SO	SO-AW	Awdal
\N	1195	6967	SO	SO-BK	Bakool
\N	1195	6968	SO	SO-BN	Banaadir
\N	1195	6969	SO	SO-BY	Bay
\N	1195	6970	SO	SO-GA	Galguduud
\N	1195	6971	SO	SO-GE	Gedo
\N	1195	6972	SO	SO-HI	Hiirsan
\N	1195	6973	SO	SO-JD	Jubbada Dhexe
\N	1195	6974	SO	SO-JH	Jubbada Hoose
\N	1195	6975	SO	SO-MU	Mudug
\N	1195	6976	SO	SO-NU	Nugaal
\N	1195	6977	SO	SO-SA	Saneag
\N	1195	6978	SO	SO-SD	Shabeellaha Dhexe
\N	1195	6979	SO	SO-SH	Shabeellaha Hoose
\N	1195	6980	SO	SO-SO	Sool
\N	1195	6981	SO	SO-TO	Togdheer
\N	1195	6982	SO	SO-WO	Woqooyi Galbeed
\N	1201	6983	SR	SR-BR	Brokopondo
\N	1201	6984	SR	SR-CM	Commewijne
\N	1201	6985	SR	SR-CR	Coronie
\N	1201	6986	SR	SR-MA	Marowijne
\N	1201	6987	SR	SR-NI	Nickerie
\N	1201	6988	SR	SR-PM	Paramaribo
\N	1201	6989	SR	SR-SA	Saramacca
\N	1201	6990	SR	SR-SI	Sipaliwini
\N	1201	6991	SR	SR-WA	Wanica
\N	1207	6992	ST	ST-P	Principe
\N	1207	6993	ST	ST-S	Sao Tome
\N	1066	6994	SV	SV-AH	Ahuachapan
\N	1066	6995	SV	SV-CA	Cabanas
\N	1066	6996	SV	SV-CU	Cuscatlan
\N	1066	6997	SV	SV-CH	Chalatenango
\N	1066	6998	SV	SV-MO	Morazan
\N	1066	6999	SV	SV-SM	San Miguel
\N	1066	7000	SV	SV-SS	San Salvador
\N	1066	7001	SV	SV-SA	Santa Ana
\N	1066	7002	SV	SV-SV	San Vicente
\N	1066	7003	SV	SV-SO	Sonsonate
\N	1066	7004	SV	SV-US	Usulutan
\N	1206	7005	SY	SY-HA	Al Hasakah
\N	1206	7006	SY	SY-LA	Al Ladhiqiyah
\N	1206	7007	SY	SY-QU	Al Qunaytirah
\N	1206	7008	SY	SY-RA	Ar Raqqah
\N	1206	7009	SY	SY-SU	As Suwayda'
\N	1206	7010	SY	SY-DR	Dar'a
\N	1206	7011	SY	SY-DY	Dayr az Zawr
\N	1206	7012	SY	SY-DI	Dimashq
\N	1206	7013	SY	SY-HL	Halab
\N	1206	7014	SY	SY-HM	Hamah
\N	1206	7015	SY	SY-HI	Jim'
\N	1206	7016	SY	SY-ID	Idlib
\N	1206	7017	SY	SY-RD	Rif Dimashq
\N	1206	7018	SY	SY-TA	Tarts
\N	1203	7019	SZ	SZ-HH	Hhohho
\N	1203	7020	SZ	SZ-LU	Lubombo
\N	1203	7021	SZ	SZ-MA	Manzini
\N	1203	7022	SZ	SZ-SH	Shiselweni
\N	1043	7023	TD	TD-BA	Batha
\N	1043	7024	TD	TD-BI	Biltine
\N	1043	7025	TD	TD-BET	Borkou-Ennedi-Tibesti
\N	1043	7026	TD	TD-CB	Chari-Baguirmi
\N	1043	7027	TD	TD-GR	Guera
\N	1043	7028	TD	TD-KA	Kanem
\N	1043	7029	TD	TD-LC	Lac
\N	1043	7030	TD	TD-LO	Logone-Occidental
\N	1043	7031	TD	TD-LR	Logone-Oriental
\N	1043	7032	TD	TD-MK	Mayo-Kebbi
\N	1043	7033	TD	TD-MC	Moyen-Chari
\N	1043	7034	TD	TD-OD	Ouaddai
\N	1043	7035	TD	TD-SA	Salamat
\N	1043	7036	TD	TD-TA	Tandjile
\N	1214	7037	TG	TG-K	Kara
\N	1214	7038	TG	TG-M	Maritime (Region)
\N	1214	7039	TG	TG-S	Savannes
\N	1211	7040	TH	TH-10	Krung Thep Maha Nakhon Bangkok
\N	1211	7041	TH	TH-S	Phatthaya
\N	1211	7042	TH	TH-37	Amnat Charoen
\N	1211	7043	TH	TH-15	Ang Thong
\N	1211	7044	TH	TH-31	Buri Ram
\N	1211	7045	TH	TH-24	Chachoengsao
\N	1211	7046	TH	TH-18	Chai Nat
\N	1211	7047	TH	TH-36	Chaiyaphum
\N	1211	7048	TH	TH-22	Chanthaburi
\N	1211	7049	TH	TH-50	Chiang Mai
\N	1211	7050	TH	TH-57	Chiang Rai
\N	1211	7051	TH	TH-20	Chon Buri
\N	1211	7052	TH	TH-86	Chumphon
\N	1211	7053	TH	TH-46	Kalasin
\N	1211	7054	TH	TH-62	Kamphasng Phet
\N	1211	7055	TH	TH-71	Kanchanaburi
\N	1211	7056	TH	TH-40	Khon Kaen
\N	1211	7057	TH	TH-81	Krabi
\N	1211	7058	TH	TH-52	Lampang
\N	1211	7059	TH	TH-51	Lamphun
\N	1211	7060	TH	TH-42	Loei
\N	1211	7061	TH	TH-16	Lop Buri
\N	1211	7062	TH	TH-58	Mae Hong Son
\N	1211	7063	TH	TH-44	Maha Sarakham
\N	1211	7064	TH	TH-49	Mukdahan
\N	1211	7065	TH	TH-26	Nakhon Nayok
\N	1211	7066	TH	TH-73	Nakhon Pathom
\N	1211	7067	TH	TH-48	Nakhon Phanom
\N	1211	7068	TH	TH-30	Nakhon Ratchasima
\N	1211	7069	TH	TH-60	Nakhon Sawan
\N	1211	7070	TH	TH-80	Nakhon Si Thammarat
\N	1211	7071	TH	TH-55	Nan
\N	1211	7072	TH	TH-96	Narathiwat
\N	1211	7073	TH	TH-39	Nong Bua Lam Phu
\N	1211	7074	TH	TH-43	Nong Khai
\N	1211	7075	TH	TH-12	Nonthaburi
\N	1211	7076	TH	TH-13	Pathum Thani
\N	1211	7077	TH	TH-94	Pattani
\N	1211	7078	TH	TH-82	Phangnga
\N	1211	7079	TH	TH-93	Phatthalung
\N	1211	7080	TH	TH-56	Phayao
\N	1211	7081	TH	TH-67	Phetchabun
\N	1211	7082	TH	TH-76	Phetchaburi
\N	1211	7083	TH	TH-66	Phichit
\N	1211	7084	TH	TH-65	Phitsanulok
\N	1211	7085	TH	TH-54	Phrae
\N	1211	7086	TH	TH-14	Phra Nakhon Si Ayutthaya
\N	1211	7087	TH	TH-83	Phaket
\N	1211	7088	TH	TH-25	Prachin Buri
\N	1211	7089	TH	TH-77	Prachuap Khiri Khan
\N	1211	7090	TH	TH-85	Ranong
\N	1211	7091	TH	TH-70	Ratchaburi
\N	1211	7092	TH	TH-21	Rayong
\N	1211	7093	TH	TH-45	Roi Et
\N	1211	7094	TH	TH-27	Sa Kaeo
\N	1211	7095	TH	TH-47	Sakon Nakhon
\N	1211	7096	TH	TH-11	Samut Prakan
\N	1211	7097	TH	TH-74	Samut Sakhon
\N	1211	7098	TH	TH-75	Samut Songkhram
\N	1211	7099	TH	TH-19	Saraburi
\N	1211	7100	TH	TH-91	Satun
\N	1211	7101	TH	TH-17	Sing Buri
\N	1211	7102	TH	TH-33	Si Sa Ket
\N	1211	7103	TH	TH-90	Songkhla
\N	1211	7104	TH	TH-64	Sukhothai
\N	1211	7105	TH	TH-72	Suphan Buri
\N	1211	7106	TH	TH-84	Surat Thani
\N	1211	7107	TH	TH-32	Surin
\N	1211	7108	TH	TH-63	Tak
\N	1211	7109	TH	TH-92	Trang
\N	1211	7110	TH	TH-23	Trat
\N	1211	7111	TH	TH-34	Ubon Ratchathani
\N	1211	7112	TH	TH-41	Udon Thani
\N	1211	7113	TH	TH-61	Uthai Thani
\N	1211	7114	TH	TH-53	Uttaradit
\N	1211	7115	TH	TH-95	Yala
\N	1211	7116	TH	TH-35	Yasothon
\N	1209	7117	TJ	TJ-SU	Sughd
\N	1209	7118	TJ	TJ-KT	Khatlon
\N	1209	7119	TJ	TJ-GB	Gorno-Badakhshan
\N	1220	7120	TM	TM-A	Ahal
\N	1220	7121	TM	TM-B	Balkan
\N	1220	7122	TM	TM-D	Dasoguz
\N	1220	7123	TM	TM-L	Lebap
\N	1220	7124	TM	TM-M	Mary
\N	1218	7125	TN	TN-31	Béja
\N	1218	7126	TN	TN-13	Ben Arous
\N	1218	7127	TN	TN-23	Bizerte
\N	1218	7128	TN	TN-81	Gabès
\N	1218	7129	TN	TN-71	Gafsa
\N	1218	7130	TN	TN-32	Jendouba
\N	1218	7131	TN	TN-41	Kairouan
\N	1218	7132	TN	TN-42	Rasserine
\N	1218	7133	TN	TN-73	Kebili
\N	1218	7134	TN	TN-12	L'Ariana
\N	1218	7135	TN	TN-33	Le Ref
\N	1218	7136	TN	TN-53	Mahdia
\N	1218	7137	TN	TN-14	La Manouba
\N	1218	7138	TN	TN-82	Medenine
\N	1218	7139	TN	TN-52	Moneatir
\N	1218	7140	TN	TN-21	Naboul
\N	1218	7141	TN	TN-61	Sfax
\N	1218	7142	TN	TN-43	Sidi Bouxid
\N	1218	7143	TN	TN-34	Siliana
\N	1218	7144	TN	TN-51	Sousse
\N	1218	7145	TN	TN-83	Tataouine
\N	1218	7146	TN	TN-72	Tozeur
\N	1218	7147	TN	TN-11	Tunis
\N	1218	7148	TN	TN-22	Zaghouan
\N	1219	7149	TR	TR-01	Adana
\N	1219	7150	TR	TR-02	Ad yaman
\N	1219	7151	TR	TR-03	Afyon
\N	1219	7152	TR	TR-04	Ag r
\N	1219	7153	TR	TR-68	Aksaray
\N	1219	7154	TR	TR-05	Amasya
\N	1219	7155	TR	TR-06	Ankara
\N	1219	7156	TR	TR-07	Antalya
\N	1219	7157	TR	TR-75	Ardahan
\N	1219	7158	TR	TR-08	Artvin
\N	1219	7159	TR	TR-09	Aydin
\N	1219	7160	TR	TR-10	Bal kesir
\N	1219	7161	TR	TR-74	Bartin
\N	1219	7162	TR	TR-72	Batman
\N	1219	7163	TR	TR-69	Bayburt
\N	1219	7164	TR	TR-11	Bilecik
\N	1219	7165	TR	TR-12	Bingol
\N	1219	7166	TR	TR-13	Bitlis
\N	1219	7167	TR	TR-14	Bolu
\N	1219	7168	TR	TR-15	Burdur
\N	1219	7169	TR	TR-16	Bursa
\N	1219	7170	TR	TR-17	Canakkale
\N	1219	7171	TR	TR-18	Cankir
\N	1219	7172	TR	TR-19	Corum
\N	1219	7173	TR	TR-20	Denizli
\N	1219	7174	TR	TR-21	Diyarbakir
\N	1219	7175	TR	TR-81	Duzce
\N	1219	7176	TR	TR-22	Edirne
\N	1219	7177	TR	TR-23	Elazig
\N	1219	7178	TR	TR-24	Erzincan
\N	1219	7179	TR	TR-25	Erzurum
\N	1219	7180	TR	TR-26	Eskis'ehir
\N	1219	7181	TR	TR-27	Gaziantep
\N	1219	7182	TR	TR-28	Giresun
\N	1219	7183	TR	TR-29	Gms'hane
\N	1219	7184	TR	TR-30	Hakkari
\N	1219	7185	TR	TR-31	Hatay
\N	1219	7186	TR	TR-76	Igidir
\N	1219	7187	TR	TR-32	Isparta
\N	1219	7188	TR	TR-33	Icel
\N	1219	7189	TR	TR-34	Istanbul
\N	1219	7190	TR	TR-35	Izmir
\N	1219	7191	TR	TR-46	Kahramanmaras
\N	1219	7192	TR	TR-78	Karabk
\N	1219	7193	TR	TR-70	Karaman
\N	1219	7194	TR	TR-36	Kars
\N	1219	7195	TR	TR-37	Kastamonu
\N	1219	7196	TR	TR-38	Kayseri
\N	1219	7197	TR	TR-71	Kirikkale
\N	1219	7198	TR	TR-39	Kirklareli
\N	1219	7199	TR	TR-40	Kirs'ehir
\N	1219	7200	TR	TR-79	Kilis
\N	1219	7201	TR	TR-41	Kocaeli
\N	1219	7202	TR	TR-42	Konya
\N	1219	7203	TR	TR-43	Ktahya
\N	1219	7204	TR	TR-44	Malatya
\N	1219	7205	TR	TR-45	Manisa
\N	1219	7206	TR	TR-47	Mardin
\N	1219	7207	TR	TR-48	Mugila
\N	1219	7208	TR	TR-49	Mus
\N	1219	7209	TR	TR-50	Nevs'ehir
\N	1219	7210	TR	TR-51	Nigide
\N	1219	7211	TR	TR-52	Ordu
\N	1219	7212	TR	TR-80	Osmaniye
\N	1219	7213	TR	TR-53	Rize
\N	1219	7214	TR	TR-54	Sakarya
\N	1219	7215	TR	TR-55	Samsun
\N	1219	7216	TR	TR-56	Siirt
\N	1219	7217	TR	TR-57	Sinop
\N	1219	7218	TR	TR-58	Sivas
\N	1219	7219	TR	TR-63	S'anliurfa
\N	1219	7220	TR	TR-73	S'rnak
\N	1219	7221	TR	TR-59	Tekirdag
\N	1219	7222	TR	TR-60	Tokat
\N	1219	7223	TR	TR-61	Trabzon
\N	1219	7224	TR	TR-62	Tunceli
\N	1219	7225	TR	TR-64	Us'ak
\N	1219	7226	TR	TR-65	Van
\N	1219	7227	TR	TR-77	Yalova
\N	1219	7228	TR	TR-66	Yozgat
\N	1219	7229	TR	TR-67	Zonguldak
\N	1217	7230	TT	TT-CTT	Couva-Tabaquite-Talparo
\N	1217	7231	TT	TT-DMN	Diego Martin
\N	1217	7232	TT	TT-ETO	Eastern Tobago
\N	1217	7233	TT	TT-PED	Penal-Debe
\N	1217	7234	TT	TT-PRT	Princes Town
\N	1217	7235	TT	TT-RCM	Rio Claro-Mayaro
\N	1217	7236	TT	TT-SGE	Sangre Grande
\N	1217	7237	TT	TT-SJL	San Juan-Laventille
\N	1217	7238	TT	TT-SIP	Siparia
\N	1217	7239	TT	TT-TUP	Tunapuna-Piarco
\N	1217	7240	TT	TT-WTO	Western Tobago
\N	1217	7241	TT	TT-ARI	Arima
\N	1217	7242	TT	TT-CHA	Chaguanas
\N	1217	7243	TT	TT-PTF	Point Fortin
\N	1217	7244	TT	TT-POS	Port of Spain
\N	1217	7245	TT	TT-SFO	San Fernando
\N	1063	7246	TL	TL-AL	Aileu
\N	1063	7247	TL	TL-AN	Ainaro
\N	1063	7248	TL	TL-BA	Bacucau
\N	1063	7249	TL	TL-BO	Bobonaro
\N	1063	7250	TL	TL-CO	Cova Lima
\N	1063	7251	TL	TL-DI	Dili
\N	1063	7252	TL	TL-ER	Ermera
\N	1063	7253	TL	TL-LA	Laulem
\N	1063	7254	TL	TL-LI	Liquica
\N	1063	7255	TL	TL-MT	Manatuto
\N	1063	7256	TL	TL-MF	Manafahi
\N	1063	7257	TL	TL-OE	Oecussi
\N	1063	7258	TL	TL-VI	Viqueque
\N	1208	7259	TW	TW-CHA	Changhua
\N	1208	7260	TW	TW-CYQ	Chiayi
\N	1208	7261	TW	TW-HSQ	Hsinchu
\N	1208	7262	TW	TW-HUA	Hualien
\N	1208	7263	TW	TW-ILA	Ilan
\N	1208	7264	TW	TW-KHQ	Kaohsiung
\N	1208	7265	TW	TW-MIA	Miaoli
\N	1208	7266	TW	TW-NAN	Nantou
\N	1208	7267	TW	TW-PEN	Penghu
\N	1208	7268	TW	TW-PIF	Pingtung
\N	1208	7269	TW	TW-TXQ	Taichung
\N	1208	7270	TW	TW-TNQ	Tainan
\N	1208	7271	TW	TW-TPQ	Taipei
\N	1208	7272	TW	TW-TTT	Taitung
\N	1208	7273	TW	TW-TAO	Taoyuan
\N	1208	7274	TW	TW-YUN	Yunlin
\N	1208	7275	TW	TW-KEE	Keelung
\N	1210	7276	TZ	TZ-01	Arusha
\N	1210	7277	TZ	TZ-02	Dar-es-Salaam
\N	1210	7278	TZ	TZ-03	Dodoma
\N	1210	7279	TZ	TZ-04	Iringa
\N	1210	7280	TZ	TZ-05	Kagera
\N	1210	7281	TZ	TZ-06	Kaskazini Pemba
\N	1210	7282	TZ	TZ-07	Kaskazini Unguja
\N	1210	7283	TZ	TZ-08	Xigoma
\N	1210	7284	TZ	TZ-09	Kilimanjaro
\N	1210	7285	TZ	TZ-10	Rusini Pemba
\N	1210	7286	TZ	TZ-11	Kusini Unguja
\N	1210	7287	TZ	TZ-12	Lindi
\N	1210	7288	TZ	TZ-26	Manyara
\N	1210	7289	TZ	TZ-13	Mara
\N	1210	7290	TZ	TZ-14	Mbeya
\N	1210	7291	TZ	TZ-15	Mjini Magharibi
\N	1210	7292	TZ	TZ-16	Morogoro
\N	1210	7293	TZ	TZ-17	Mtwara
\N	1210	7294	TZ	TZ-19	Pwani
\N	1210	7295	TZ	TZ-20	Rukwa
\N	1210	7296	TZ	TZ-21	Ruvuma
\N	1210	7297	TZ	TZ-22	Shinyanga
\N	1210	7298	TZ	TZ-23	Singida
\N	1210	7299	TZ	TZ-24	Tabora
\N	1210	7300	TZ	TZ-25	Tanga
\N	1224	7301	UA	UA-71	Cherkas'ka Oblast'
\N	1224	7302	UA	UA-74	Chernihivs'ka Oblast'
\N	1224	7303	UA	UA-77	Chernivets'ka Oblast'
\N	1224	7304	UA	UA-12	Dnipropetrovs'ka Oblast'
\N	1224	7305	UA	UA-14	Donets'ka Oblast'
\N	1224	7306	UA	UA-26	Ivano-Frankivs'ka Oblast'
\N	1224	7307	UA	UA-63	Kharkivs'ka Oblast'
\N	1224	7308	UA	UA-65	Khersons'ka Oblast'
\N	1224	7309	UA	UA-68	Khmel'nyts'ka Oblast'
\N	1224	7310	UA	UA-35	Kirovohrads'ka Oblast'
\N	1224	7311	UA	UA-32	Kyivs'ka Oblast'
\N	1224	7312	UA	UA-09	Luhans'ka Oblast'
\N	1224	7313	UA	UA-46	L'vivs'ka Oblast'
\N	1224	7314	UA	UA-48	Mykolaivs'ka Oblast'
\N	1224	7315	UA	UA-51	Odes 'ka Oblast'
\N	1224	7316	UA	UA-53	Poltavs'ka Oblast'
\N	1224	7317	UA	UA-56	Rivnens'ka Oblast'
\N	1224	7318	UA	UA-59	Sums 'ka Oblast'
\N	1224	7319	UA	UA-61	Ternopil's'ka Oblast'
\N	1224	7320	UA	UA-05	Vinnyts'ka Oblast'
\N	1224	7321	UA	UA-07	Volyos'ka Oblast'
\N	1224	7322	UA	UA-21	Zakarpats'ka Oblast'
\N	1224	7323	UA	UA-23	Zaporiz'ka Oblast'
\N	1224	7324	UA	UA-18	Zhytomyrs'ka Oblast'
\N	1224	7325	UA	UA-43	Respublika Krym
\N	1224	7326	UA	UA-30	Kyiv
\N	1224	7327	UA	UA-40	Sevastopol
\N	1223	7328	UG	UG-301	Adjumani
\N	1223	7329	UG	UG-302	Apac
\N	1223	7330	UG	UG-303	Arua
\N	1223	7331	UG	UG-201	Bugiri
\N	1223	7332	UG	UG-401	Bundibugyo
\N	1223	7333	UG	UG-402	Bushenyi
\N	1223	7334	UG	UG-202	Busia
\N	1223	7335	UG	UG-304	Gulu
\N	1223	7336	UG	UG-403	Hoima
\N	1223	7337	UG	UG-203	Iganga
\N	1223	7338	UG	UG-204	Jinja
\N	1223	7339	UG	UG-404	Kabale
\N	1223	7340	UG	UG-405	Kabarole
\N	1223	7341	UG	UG-213	Kaberamaido
\N	1223	7342	UG	UG-101	Kalangala
\N	1223	7343	UG	UG-102	Kampala
\N	1223	7344	UG	UG-205	Kamuli
\N	1223	7345	UG	UG-413	Kamwenge
\N	1223	7346	UG	UG-414	Kanungu
\N	1223	7347	UG	UG-206	Kapchorwa
\N	1223	7348	UG	UG-406	Kasese
\N	1223	7349	UG	UG-207	Katakwi
\N	1223	7350	UG	UG-112	Kayunga
\N	1223	7351	UG	UG-407	Kibaale
\N	1223	7352	UG	UG-103	Kiboga
\N	1223	7353	UG	UG-408	Kisoro
\N	1223	7354	UG	UG-305	Kitgum
\N	1223	7355	UG	UG-306	Kotido
\N	1223	7356	UG	UG-208	Kumi
\N	1223	7357	UG	UG-415	Kyenjojo
\N	1223	7358	UG	UG-307	Lira
\N	1223	7359	UG	UG-104	Luwero
\N	1223	7360	UG	UG-105	Masaka
\N	1223	7361	UG	UG-409	Masindi
\N	1223	7362	UG	UG-214	Mayuge
\N	1223	7363	UG	UG-209	Mbale
\N	1223	7364	UG	UG-410	Mbarara
\N	1223	7365	UG	UG-308	Moroto
\N	1223	7366	UG	UG-309	Moyo
\N	1223	7367	UG	UG-106	Mpigi
\N	1223	7368	UG	UG-107	Mubende
\N	1223	7369	UG	UG-108	Mukono
\N	1223	7370	UG	UG-311	Nakapiripirit
\N	1223	7371	UG	UG-109	Nakasongola
\N	1223	7372	UG	UG-310	Nebbi
\N	1223	7373	UG	UG-411	Ntungamo
\N	1223	7374	UG	UG-312	Pader
\N	1223	7375	UG	UG-210	Pallisa
\N	1223	7376	UG	UG-110	Rakai
\N	1223	7377	UG	UG-412	Rukungiri
\N	1223	7378	UG	UG-111	Sembabule
\N	1223	7379	UG	UG-215	Sironko
\N	1223	7380	UG	UG-211	Soroti
\N	1223	7381	UG	UG-212	Tororo
\N	1223	7382	UG	UG-113	Wakiso
\N	1223	7383	UG	UG-313	Yumbe
\N	1227	7384	UM	UM-81	Baker Island
\N	1227	7385	UM	UM-84	Howland Island
\N	1227	7386	UM	UM-86	Jarvis Island
\N	1227	7387	UM	UM-67	Johnston Atoll
\N	1227	7388	UM	UM-89	Kingman Reef
\N	1227	7389	UM	UM-71	Midway Islands
\N	1227	7390	UM	UM-76	Navassa Island
\N	1227	7391	UM	UM-95	Palmyra Atoll
\N	1227	7392	UM	UM-79	Wake Ialand
1014	1228	7410	US	US-IA	Iowa
1558	1228	7439	US	US-UM	United States Minor Outlying Islands
1559	1228	7448	US	US-AE	Armed Forces Europe
1560	1228	7449	US	US-AA	Armed Forces Americas
1561	1228	7450	US	US-AP	Armed Forces Pacific
\N	1229	7451	UY	UY-AR	Artigsa
\N	1229	7452	UY	UY-CA	Canelones
\N	1229	7453	UY	UY-CL	Cerro Largo
\N	1229	7454	UY	UY-CO	Colonia
\N	1229	7455	UY	UY-DU	Durazno
\N	1229	7456	UY	UY-FS	Flores
\N	1229	7457	UY	UY-LA	Lavalleja
\N	1229	7458	UY	UY-MA	Maldonado
\N	1229	7459	UY	UY-MO	Montevideo
\N	1229	7460	UY	UY-PA	Paysandu
\N	1229	7461	UY	UY-RV	Rivera
\N	1229	7462	UY	UY-RO	Rocha
\N	1229	7463	UY	UY-SA	Salto
\N	1229	7464	UY	UY-SO	Soriano
\N	1229	7465	UY	UY-TA	Tacuarembo
\N	1229	7466	UY	UY-TT	Treinta y Tres
\N	1230	7467	UZ	UZ-TK	Toshkent 
\N	1230	7468	UZ	UZ-QR	Qoraqalpogiston Respublikasi
\N	1230	7469	UZ	UZ-AN	Andijon
\N	1230	7470	UZ	UZ-BU	Buxoro
\N	1230	7471	UZ	UZ-FA	Farg'ona
\N	1230	7472	UZ	UZ-JI	Jizzax
\N	1230	7473	UZ	UZ-KH	Khorazm
\N	1230	7474	UZ	UZ-NG	Namangan
\N	1230	7475	UZ	UZ-NW	Navoiy
\N	1230	7476	UZ	UZ-QA	Qashqadaryo
\N	1230	7477	UZ	UZ-SA	Samarqand
\N	1230	7478	UZ	UZ-SI	Sirdaryo
\N	1230	7479	UZ	UZ-SU	Surxondaryo
\N	1230	7480	UZ	UZ-TO	Toshkent
\N	1230	7481	UZ	UZ-XO	Xorazm
\N	1232	7482	VE	VE-A	Diatrito Federal
\N	1232	7483	VE	VE-B	Anzoategui
\N	1232	7484	VE	VE-C	Apure
\N	1232	7485	VE	VE-D	Aragua
\N	1232	7486	VE	VE-E	Barinas
\N	1232	7487	VE	VE-G	Carabobo
\N	1232	7488	VE	VE-H	Cojedes
\N	1232	7489	VE	VE-I	Falcon
\N	1232	7490	VE	VE-J	Guarico
\N	1232	7491	VE	VE-K	Lara
\N	1232	7492	VE	VE-L	Merida
\N	1232	7493	VE	VE-M	Miranda
\N	1232	7494	VE	VE-N	Monagas
\N	1232	7495	VE	VE-O	Nueva Esparta
\N	1232	7496	VE	VE-P	Portuguesa
\N	1232	7497	VE	VE-S	Tachira
\N	1232	7498	VE	VE-T	Trujillo
\N	1232	7499	VE	VE-X	Vargas
\N	1232	7500	VE	VE-U	Yaracuy
\N	1232	7501	VE	VE-V	Zulia
\N	1232	7502	VE	VE-Y	Delta Amacuro
\N	1232	7503	VE	VE-W	Dependencias Federales
\N	1233	7504	VN	VN-44	An Giang
\N	1233	7505	VN	VN-43	Ba Ria - Vung Tau
\N	1233	7506	VN	VN-53	Bac Can
\N	1233	7507	VN	VN-54	Bac Giang
\N	1233	7508	VN	VN-55	Bac Lieu
\N	1233	7509	VN	VN-56	Bac Ninh
\N	1233	7510	VN	VN-50	Ben Tre
\N	1233	7511	VN	VN-31	Binh Dinh
\N	1233	7512	VN	VN-57	Binh Duong
\N	1233	7513	VN	VN-58	Binh Phuoc
\N	1233	7514	VN	VN-40	Binh Thuan
\N	1233	7515	VN	VN-59	Ca Mau
\N	1233	7516	VN	VN-48	Can Tho
\N	1233	7517	VN	VN-04	Cao Bang
\N	1233	7518	VN	VN-60	Da Nang, thanh pho
\N	1233	7520	VN	VN-39	Dong Nai
\N	1233	7521	VN	VN-45	Dong Thap
\N	1233	7522	VN	VN-30	Gia Lai
\N	1233	7523	VN	VN-03	Ha Giang
\N	1233	7524	VN	VN-63	Ha Nam
\N	1233	7525	VN	VN-64	Ha Noi, thu do
\N	1233	7526	VN	VN-15	Ha Tay
\N	1233	7527	VN	VN-23	Ha Tinh
\N	1233	7528	VN	VN-61	Hai Duong
\N	1233	7529	VN	VN-62	Hai Phong, thanh pho
\N	1233	7530	VN	VN-14	Hoa Binh
\N	1233	7531	VN	VN-65	Ho Chi Minh, thanh pho [Sai Gon]
\N	1233	7532	VN	VN-66	Hung Yen
\N	1233	7533	VN	VN-34	Khanh Hoa
\N	1233	7534	VN	VN-47	Kien Giang
\N	1233	7535	VN	VN-28	Kon Tum
\N	1233	7536	VN	VN-01	Lai Chau
\N	1233	7537	VN	VN-35	Lam Dong
\N	1233	7538	VN	VN-09	Lang Son
\N	1233	7539	VN	VN-02	Lao Cai
\N	1233	7540	VN	VN-41	Long An
\N	1233	7541	VN	VN-67	Nam Dinh
\N	1233	7542	VN	VN-22	Nghe An
\N	1233	7543	VN	VN-18	Ninh Binh
\N	1233	7544	VN	VN-36	Ninh Thuan
\N	1233	7545	VN	VN-68	Phu Tho
\N	1233	7546	VN	VN-32	Phu Yen
\N	1233	7547	VN	VN-24	Quang Binh
\N	1233	7548	VN	VN-27	Quang Nam
\N	1233	7549	VN	VN-29	Quang Ngai
\N	1233	7550	VN	VN-13	Quang Ninh
\N	1233	7551	VN	VN-25	Quang Tri
\N	1233	7552	VN	VN-52	Soc Trang
\N	1233	7553	VN	VN-05	Son La
\N	1233	7554	VN	VN-37	Tay Ninh
\N	1233	7555	VN	VN-20	Thai Binh
\N	1233	7556	VN	VN-69	Thai Nguyen
\N	1233	7557	VN	VN-21	Thanh Hoa
\N	1233	7558	VN	VN-26	Thua Thien-Hue
\N	1233	7559	VN	VN-46	Tien Giang
\N	1233	7560	VN	VN-51	Tra Vinh
\N	1233	7561	VN	VN-07	Tuyen Quang
\N	1233	7562	VN	VN-49	Vinh Long
\N	1233	7563	VN	VN-70	Vinh Phuc
\N	1233	7564	VN	VN-06	Yen Bai
\N	1231	7565	VU	VU-MAP	Malampa
\N	1231	7566	VU	VU-PAM	Penama
\N	1231	7567	VU	VU-SAM	Sanma
\N	1231	7568	VU	VU-SEE	Shefa
\N	1231	7569	VU	VU-TAE	Tafea
\N	1231	7570	VU	VU-TOB	Torba
\N	1185	7571	WS	WS-AA	A'ana
\N	1185	7572	WS	WS-AL	Aiga-i-le-Tai
\N	1185	7573	WS	WS-AT	Atua
\N	1185	7574	WS	WS-FA	Fa'aaaleleaga
\N	1185	7575	WS	WS-GE	Gaga'emauga
\N	1185	7576	WS	WS-GI	Gagaifomauga
\N	1185	7577	WS	WS-PA	Palauli
\N	1185	7578	WS	WS-SA	Satupa'itea
\N	1185	7579	WS	WS-TU	Tuamasaga
\N	1185	7580	WS	WS-VF	Va'a-o-Fonoti
\N	1185	7581	WS	WS-VS	Vaisigano
\N	1238	7582	CS	CS-CG	Crna Gora
\N	1238	7583	CS	CS-SR	Srbija
\N	1238	7584	CS	CS-KM	Kosovo-Metohija
\N	1238	7585	CS	CS-VO	Vojvodina
\N	1237	7586	YE	YE-AB	Abyan
\N	1237	7587	YE	YE-AD	Adan
\N	1237	7588	YE	YE-DA	Ad Dali
\N	1237	7589	YE	YE-BA	Al Bayda'
\N	1237	7590	YE	YE-MU	Al Hudaydah
\N	1237	7591	YE	YE-MR	Al Mahrah
\N	1237	7592	YE	YE-MW	Al Mahwit
\N	1237	7593	YE	YE-AM	Amran
\N	1237	7594	YE	YE-DH	Dhamar
\N	1237	7595	YE	YE-HD	Hadramawt
\N	1237	7596	YE	YE-HJ	Hajjah
\N	1237	7597	YE	YE-IB	Ibb
\N	1237	7598	YE	YE-LA	Lahij
\N	1237	7599	YE	YE-MA	Ma'rib
\N	1237	7600	YE	YE-SD	Sa'dah
\N	1237	7601	YE	YE-SN	San'a'
\N	1237	7602	YE	YE-SH	Shabwah
\N	1237	7603	YE	YE-TA	Ta'izz
\N	1196	7604	ZA	ZA-EC	Eastern Cape
\N	1196	7605	ZA	ZA-FS	Free State
\N	1196	7606	ZA	ZA-GT	Gauteng
\N	1196	7607	ZA	ZA-NL	Kwazulu-Natal
\N	1196	7608	ZA	ZA-MP	Mpumalanga
\N	1196	7609	ZA	ZA-NC	Northern Cape
\N	1196	7610	ZA	ZA-NP	Limpopo
\N	1196	7611	ZA	ZA-WC	Western Cape
\N	1239	7612	ZM	ZM-08	Copperbelt
\N	1239	7613	ZM	ZM-04	Luapula
\N	1239	7614	ZM	ZM-09	Lusaka
\N	1239	7615	ZM	ZM-06	North-Western
\N	1240	7616	ZW	ZW-BU	Bulawayo
\N	1240	7617	ZW	ZW-HA	Harare
\N	1240	7618	ZW	ZW-MA	Manicaland
\N	1240	7619	ZW	ZW-MC	Mashonaland Central
\N	1240	7620	ZW	ZW-ME	Mashonaland East
\N	1240	7621	ZW	ZW-MW	Mashonaland West
\N	1240	7622	ZW	ZW-MV	Masvingo
\N	1240	7623	ZW	ZW-MN	Matabeleland North
\N	1240	7624	ZW	ZW-MS	Matabeleland South
\N	1240	7625	ZW	ZW-MI	Midlands
\.


--
-- TOC entry 8 (OID 160711)
-- Name: countries_pkey; Type: CONSTRAINT; Schema: public; Owner: shot
--

ALTER TABLE ONLY countries
    ADD CONSTRAINT countries_pkey PRIMARY KEY (country_id);


--
-- TOC entry 9 (OID 160713)
-- Name: provinces_pkey; Type: CONSTRAINT; Schema: public; Owner: shot
--

ALTER TABLE ONLY provinces
    ADD CONSTRAINT provinces_pkey PRIMARY KEY (province_id);


--
-- TOC entry 7 (OID 156684)
-- Name: provinces_province_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shot
--

SELECT pg_catalog.setval('provinces_province_id_seq', 1, false);


SET SESSION AUTHORIZATION 'postgres';

--
-- TOC entry 3 (OID 2200)
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


