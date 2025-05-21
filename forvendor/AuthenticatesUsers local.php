<?php

namespace Illuminate\Foundation\Auth;


use App\Models\User;
use App\Models\Agent;
use App\Models\Profil;
use App\Models\Section;
use App\Models\Fonction;
use App\Models\Structure;
use App\Models\TypeProfil;
use App\Models\AgentSection;
use App\Models\Exercice;
use App\Models\Organisation;
use Illuminate\Http\Request;
use App\Models\ProfilFonction;
use App\Models\TypeOrganisation;
use Illuminate\Http\JsonResponse;
use App\Models\StatutAgentSection;
use App\Models\StatutOrganisation;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\StatutProfilFonction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TypeStatutAgentSection;
use App\Models\TypeStatutOrganisation;
use App\Models\TypeStatutProfilFonction;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Encryption\DecryptException;

trait AuthenticatesUsers
{
    use RedirectsUsers, ThrottlesLogins;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        ?>
            <script>alert('Veuillez utiliser le navigateur Google Chrome');</script>
        <?php
        return redirect()->back()->with('error','Veuillez utiliser le navigateur Google Chrome');

        //dd($request);
        //n41956
        $error = null;
        $key = "071234567890";
        //$2y$10$sDqDW6MGTjsfgv5Ra8x.outPF7J3NQk8/o9BUVNunZ9m5Um6sBfLW
        
        

        $injectionSql = $this->getFiltre($this->cryptoJsAesDecrypt($key, $request->username));
        if ($injectionSql != null) {
            return redirect()->back()->with('error', "Le caractère ".$injectionSql." n'est pas autorisé.");
        }

        $injectionSql = $this->getFiltre($this->cryptoJsAesDecrypt($key, $request->password));
        if ($injectionSql != null) {
            return redirect()->back()->with('error', "Le caractère ".$injectionSql." n'est pas autorisé.");
        }
        //$password = $this->cryptoJsAesDecrypt($key, $request->password);
        //dd(Hash::make($password));
        $ldapHost = "10.0.0.10";
        $ldapPort = 389;
        $ldapBaseDn = "dc=cnps,dc=ci";

        $adServer = "ldap://".$ldapHost;
        $username = Crypt::encryptString(strtolower(trim($this->cryptoJsAesDecrypt($key, $request->username))));
        $password = Crypt::encryptString(trim($this->cryptoJsAesDecrypt($key, $request->password)));

        //dd(Hash::make('1234567890'),$username,Crypt::decryptString($username));

         //vérifier si ce compte existe
         $user_verif = DB::table('users')
         ->where('username',Crypt::decryptString($username))
         // ->where('password',Hash::make($password))
         ->where('flag_actif',1)
         ->first();
         if ($user_verif!=null) {
         
             $error = null;
             $this->storeSessionData($request,$user_verif->id);
         }else{
             $error = "Erreur de connexion";
         }


        if ($error!=null) {
            return redirect()->back()->with('error',$error);
        }
        
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        ///return $request->only($this->username(), 'password');

        $key = "071234567890";
        $new_only['username'] = $this->cryptoJsAesDecrypt($key, $request->username);
        $new_only['password'] = $this->cryptoJsAesDecrypt($key, $request->password);

        return $new_only;
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 204)
                    : redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    public function getAgentAuthenticate($mle,$nom_prenoms){

        $agent = Agent::where('mle',$mle)->first();
        if ($agent!=null) {
            $agents_id = $agent->id;
        }else{
            $agents_id = Agent::create([
                'mle'=>$mle,
                'nom_prenoms'=>$nom_prenoms,
            ])->id;
        }

        return $agents_id;

    }

    public function getUserAuthenticate($agents_id,$email,$password,$username,$annuaire){

        
        $user = User::where('agents_id',$agents_id)->first();
        
        if ($user!=null) {

            $users_id = $user->id;

            User::where('id',$users_id)->update([
                'email'=>$email,
                'password'=>Hash::make(Crypt::decryptString($password)),
                'annuaire'=>$annuaire
            ]);

        }else{

            $users_id = User::create([
                'agents_id'=>$agents_id,
                'username'=>Crypt::decryptString($username),
                'email'=>$email,
                'password'=>Hash::make(Crypt::decryptString($password)),
                'flag_actif'=>1,
                'annuaire'=>$annuaire
            ])->id;

            

        }

        return $users_id;


    }

    public function getProfilAuthenticate($users_id){

        $profil = Profil::where('users_id',$users_id)->first();

        if ($profil!=null) {

            $profils_id = $profil->id;

        }else{

            $type_profils_id = $this->getTypeProfil();

            $profils_id = Profil::create([
                'users_id'=>$users_id,
                'type_profils_id'=>$type_profils_id,
            ])->id;

        }

        return $profils_id;

    }

    public function getTypeProfil(){

        $type_profils_id = null;

        $count_admin = count(DB::table('type_profils as tp')
                                ->join('profils as p','p.type_profils_id','=','tp.id')
                                ->where('tp.name','Administrateur fonctionnel')->get());
        if ($count_admin === 0) {
            $type_profils_name = "Administrateur fonctionnel";
        }else{
            $type_profils_name = "Agent Cnps";
        }

        if (isset($type_profils_name)) {

            $type_profil = TypeProfil::where('name',$type_profils_name)->first();

            if ($type_profil!=null) {
                $type_profils_id = $type_profil->id;
            }else{
                $type_profils_id = TypeProfil::create([
                    'name'=>$type_profils_name,
                ])->id;
            }
            

        }

        return $type_profils_id;

    }

    public function getStructureAuthenticate($nom_structure){

        $structure = Structure::where('nom_structure',$nom_structure)->first();

        if ($structure!=null) {
            $code_structure = $structure->code_structure;
        }else{
            $code_structure = null;
        }

        return $code_structure;
    }

    public function getSectionAuthenticate($code_structure,$nom_structure){

        $section = Section::where('code_structure',$code_structure)
            ->orderBy('id')
            ->first();
            if ($section!=null) {
                $sections_id = $section->id;
            }else{

                $code_section = $code_structure.'01';
                $nom_section = 'SECTION COMMUNE'.$nom_structure;

                $sections_id = Section::create([
                    'code_section',$code_section,
                    'code_structure',$code_structure,
                    'nom_section',$nom_section,
                    'code_gestion','G',
                ])->id;

            }

        return $sections_id;

    }

    public function getAgentSectionAuthenticate($agents_id,$sections_id,$exercice,$profils_id,$code_structure){
        
        $agent_section = AgentSection::where('agents_id',$agents_id)
        ->where('sections_id',$sections_id)
        ->first();

        if ($agent_section!=null) {

            $libelle = 'Désactivé';
            
            $this->statut_agent_section_desactive($agents_id,$profils_id,$libelle,$code_structure);

            $agent_sections_id = $agent_section->id;

            $libelle = 'Activé';
            $this->statut_agent_section_active($agent_sections_id,$profils_id,$libelle);


        }else{

            $libelle = 'Désactivé';

            $this->statut_agent_section_desactive($agents_id,$profils_id,$libelle,$code_structure);

            $agent_sections_id = AgentSection::create([
                'agents_id'=>$agents_id,
                'sections_id'=>$sections_id,
                'exercice'=>$exercice,
            ])->id;

            $libelle = 'Activé';
            $this->statut_agent_section_active($agent_sections_id,$profils_id,$libelle);

        }

        return $agent_sections_id;
    }

    public function statut_agent_section_desactive($agents_id,$profils_id,$libelle,$code_structure){

        $type_statut_agent_section = TypeStatutAgentSection::where('libelle',$libelle)
                ->first();
                if ($type_statut_agent_section!=null) {

                    $type_statut_agent_sections_id = $type_statut_agent_section->id;

                }else{

                    $type_statut_agent_sections_id = TypeStatutAgentSection::create([
                        'libelle'=>$libelle
                    ])->id;

                }

                
                StatutAgentSection::where('ase.agents_id',$agents_id)
                ->join('agent_sections as ase','ase.id','=','agent_sections_id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('structures as st','st.code_structure','=','s.code_structure')
                ->join('type_statut_agent_sections as tsas','tsas.id','=','type_statut_agent_sections_id')
                ->where('tsas.libelle','Activé')
                ->whereNotIn('st.code_structure',[$code_structure])
                ->update([
                    'type_statut_agent_sections_id'=>$type_statut_agent_sections_id,
                    'date_fin'=>date("Y-m-d"),
                    'profils_id'=>$profils_id,
                ]);
    }

    public function statut_agent_section_active($agent_sections_id,$profils_id,$libelle){

        $type_statut_agent_section = TypeStatutAgentSection::where('libelle',$libelle)
                ->first();
                if ($type_statut_agent_section!=null) {

                    $type_statut_agent_sections_id = $type_statut_agent_section->id;

                }else{

                    $type_statut_agent_sections_id = TypeStatutAgentSection::create([
                        'libelle'=>$libelle
                    ])->id;

                }

                $statut_agent_section = StatutAgentSection::where('agent_sections_id',$agent_sections_id)
                ->where('type_statut_agent_sections_id',$type_statut_agent_sections_id)
                ->first();
                if ($statut_agent_section===null) {
                    StatutAgentSection::create([
                        'agent_sections_id'=>$agent_sections_id,
                        'type_statut_agent_sections_id'=>$type_statut_agent_sections_id,
                        'date_debut'=>date("Y-m-d"),
                        'profils_id'=>$profils_id,
                    ]);
                }

                

    }

    public function storeSessionData(Request $request,$users_id){

        $type_profil = DB::table('type_profils as tp')
        ->join('profils as p','p.type_profils_id','=','tp.id')
        ->join('users as u','u.id','=','p.users_id')
        ->where('u.id',$users_id)
        ->where('p.flag_actif',1)
        ->where('tp.name','Agent Cnps')
        ->select('p.id as profils_id','u.agents_id')
        ->first();
        if ($type_profil!=null) {

            $profils_id = $type_profil->profils_id;
            $agents_id = $type_profil->agents_id;

            

        }else{
            $type_profil_autre = DB::table('type_profils as tp')
            ->join('profils as p','p.type_profils_id','=','tp.id')
            ->join('users as u','u.id','=','p.users_id')
            ->where('u.id',$users_id)
            ->where('p.flag_actif',1)
            ->select('p.id as profils_id','u.agents_id')
            ->first();
            if ($type_profil_autre!=null) {
                $profils_id = $type_profil_autre->profils_id;
                $agents_id = $type_profil_autre->agents_id;
            }
        }

        $this->setSessionFonction($agents_id,$request);


        $request->session()->put('profils_id',$profils_id);
    } 

    public function getFonctionAuthenticate($libelle){

        $fonction = Fonction::where('libelle',$libelle)->first();

        if ($fonction!=null) {

            $fonctions_id = $fonction->id;

        }else{

            $fonctions_id = Fonction::create([
                'libelle'=>$libelle,
            ])->id;

        }

        return $fonctions_id;

    }

    public function setProfilFonctionAuthenticate($agents_id,$fonctions_id,$profils_id){

        $profil_fonction = ProfilFonction::where('agents_id',$agents_id)
            ->where('fonctions_id',$fonctions_id)
            ->first();
        if ($profil_fonction!=null) {

            $profil_fonctions_id = $profil_fonction->id;
            if ($profil_fonction->flag_actif != 1) {

                ProfilFonction::where('id',$profil_fonction->id)
                ->update([
                    'flag_actif'=>1
                ]);

            }

        }else{
            $profil_fonctions_id = ProfilFonction::create([
                'agents_id'=>$agents_id,
                'fonctions_id'=>$fonctions_id,
                'flag_actif'=>1
            ])->id;
        }

        if(isset($profil_fonctions_id)){

            $libelle = "Activé";
            $type_statut_profil_fonctions_id = $this->getTypeStatutProfilFonction($libelle);

            if (isset($type_statut_profil_fonctions_id)) {

                $date_debut = date("Y-m-d");
                $date_fin = null;
                $commentaire = "Créé à la connexion";

                $this->setStatutProfilFonction($profil_fonctions_id,$type_statut_profil_fonctions_id,$profils_id,$date_debut,$date_fin,$commentaire);

            }
        }
    }

    public function getTypeStatutProfilFonction($libelle){

        $type_statut_profil_fonction = TypeStatutProfilFonction::where('libelle',$libelle)
        ->first();

        if ($type_statut_profil_fonction!=null) {

            $type_statut_profil_fonctions_id = $type_statut_profil_fonction->id;

        }else{

            $type_statut_profil_fonctions_id = TypeStatutProfilFonction::create([
                'libelle'=>$libelle,
            ])->id;

        }

        return $type_statut_profil_fonctions_id;
    }

    public function setStatutProfilFonction($profil_fonctions_id,$type_statut_profil_fonctions_id,$profils_id,$date_debut,$date_fin,$commentaire){

        $data = [
            'profil_fonctions_id'=>$profil_fonctions_id,
            'type_statut_profil_fonctions_id'=>$type_statut_profil_fonctions_id,
            'profils_id'=>$profils_id,
            'date_debut'=>$date_debut,
            'date_fin'=>$date_fin,
            'commentaire'=>$commentaire,
        ];
        $statut_profil_fonction = DB::table('statut_profil_fonctions as spf')
        ->join('type_statut_profil_fonctions as tspf','tspf.id','=','spf.type_statut_profil_fonctions_id')
        ->where('profil_fonctions_id',$profil_fonctions_id)
        ->orderByDesc('spf.id')
        ->limit(1)
        ->select('tspf.libelle')
        ->first();

        if ($statut_profil_fonction!=null) {
            $libelle = $statut_profil_fonction->libelle;

            if ($libelle!='Activé') {
                StatutProfilFonction::create($data);
            }
        }else{
            StatutProfilFonction::create($data);
        }
    }

    public function setSessionFonction($agents_id,$request){
        
        if (isset($agents_id)) {
            $profil_fonction = ProfilFonction::where('agents_id',$agents_id)
            ->where('flag_actif',1)
            ->first();
            if ($profil_fonction!=null) {
                $fonctions_id = $profil_fonction->fonctions_id;

                
                $request->session()->put('fonctions_id',$fonctions_id);
            }
        }
    }

    public function getAgentFrs($mle,$nom_prenoms,$sigle){

        $sigle = trim($sigle);
        $mle = trim($mle);
        $mle = 'frs'.$mle;
        $nom_prenoms = trim($nom_prenoms);

        if (!empty($sigle)) {
            $nom_prenoms = $sigle;
        }

        $agent = Agent::where('mle',$mle)->first();
        if ($agent!=null) {
            
            $agents_id = $agent->id;

        }else{
            
            $agents_id = Agent::create([
                'mle'=>$mle,
                'nom_prenoms'=>$nom_prenoms,
            ])->id;
        
        }

        return $agents_id;

    }

    public function getUserFrs($agents_id,$password,$username,$secu,$email){
        
        
        $user = User::where('agents_id',$agents_id)->first();

        
        
        
        if ($user!=null) {

            $users_id = $user->id;
            
            User::where('id',$users_id)->update([
                //'email'=>$email,
                'secu'=>$secu
            ]);


        }else{

            $users_id = User::create([
                'agents_id'=>$agents_id,
                'username'=>Crypt::decryptString($username),
                'email'=>$email,
                'password'=>Hash::make(Crypt::decryptString($password)),
                'flag_actif'=>1,
                'reset'=>1,
                'secu'=>$secu
            ])->id;

            

            

        }

        return $users_id;


    }

    public function getOrganisationAuthenticate($entnum,$entcptcont,$entraisoc,$entsigle,$entadresphy,$entteleph,$tel_portable){
        

        

        $contacts = null;
        $organisations_id = null;

        if (isset($tel_portable)) {
            $contacts = trim($tel_portable);
        }

        if (isset($entteleph)) {

            if ($contacts != null) {
                $contacts = $contacts . ' / ' . trim($entteleph);
            }else{
                $contacts = trim($entteleph);
            }
            
        }

        

        

        $organisation = Organisation::where('entnum',$entnum)->first();
        if ($organisation != null) {
            $organisations_id = $organisation->id;

            Organisation::where('id',$organisations_id)
            ->update([
                'denomination'=>trim($entraisoc),
                'sigle'=>trim($entsigle),
                'contacts'=>trim($contacts),
                'adresse'=>trim($entadresphy),
                'num_contribuable'=>trim($entcptcont),  
            ]);

        }else{

            

            $libelle = 'Fournisseur';

            $type_organisation = TypeOrganisation::where('libelle',$libelle)
            ->first();

            

            if ($type_organisation!=null) {

                $type_organisations_id = $type_organisation->id;

            }else{

                

                $type_organisations_id = TypeOrganisation::create([
                    'libelle'=>$libelle
                ])->id;


            }

            $organisations_id = Organisation::create([
                'entnum'=>trim($entnum),
                'denomination'=>trim($entraisoc),
                'sigle'=>trim($entsigle),
                'type_organisations_id'=>trim($type_organisations_id),
                'contacts'=>trim($contacts),
                'adresse'=>trim($entadresphy),
                'num_contribuable'=>trim($entcptcont),                
            ])->id;

        }

        return $organisations_id;
    }

    public function getProfilFrs($users_id){

        $profil = Profil::where('users_id',$users_id)->first();

        if ($profil!=null) {

            $profils_id = $profil->id;

        }else{

            $type_profil = TypeProfil::where('name','Fournisseur')->first();

            if ($type_profil!=null) {
                $type_profils_id = $type_profil->id;
            }else{
                $type_profils_id = TypeProfil::create([
                    'name'=>'Fournisseur',
                ])->id;
            }

            $profils_id = Profil::create([
                'users_id'=>$users_id,
                'type_profils_id'=>$type_profils_id,
            ])->id;

        }

        return $profils_id;

    }

    public function statut_organisation($organisations_id,$libelle,$profils_id,$profils_ids){

        $type_statut_organisation = TypeStatutOrganisation::where('libelle',$libelle)->first();
        if ($type_statut_organisation!=null) {
            $type_statut_organisations_id = $type_statut_organisation->id;
        }else{
            $type_statut_organisations_id = TypeStatutOrganisation::create([
                'libelle'=>$libelle
            ])->id;
        }

        if (isset($type_statut_organisations_id)) {

            $statut_org = StatutOrganisation::where('organisations_id',$organisations_id)
                    ->where('type_statut_organisations_id',$type_statut_organisations_id)
                    ->where('profils_id',$profils_id)
                    ->first();
            if ($statut_org===null) {
                
                StatutOrganisation::create([
                    'organisations_id'=>$organisations_id,
                    'type_statut_organisations_id'=>$type_statut_organisations_id,
                    'date_debut'=>date('Y-m-d'),
                    'profils_id'=>$profils_id,
                    'profils_ids'=>$profils_ids,
                ]);

            }
            
            

        }
    }

    public function storeExerciceAuthenticate($exercice){
        
        $exer = Exercice::where('exercice',$exercice)->first();
        if ($exer != null) {
            $exercice = $exer->exercice;
        }else{
            $exercice = Exercice::create([
                'exercice'=>$exercice
            ])->exercice;
        }

        return $exercice;
    }

    function getFiltre($saisie){
        $injectionSql = null;
        $arrayString = array("=","<",">","?",";","'","(",")","[","]");

        for($i = 0; $i < count($arrayString); $i++)
        {
            
            $explode = explode($arrayString[$i],$saisie);
            if (isset($explode[1])) {
                $injectionSql = $arrayString[$i];
            }
        }

        return $injectionSql;
    }

    public function cryptoJsAesDecrypt($passphrase, $jsonString){
        $jsondata = json_decode($jsonString, true);
        $salt = hex2bin($jsondata["s"]);
        $ct = base64_decode($jsondata["ct"]);
        $iv  = hex2bin($jsondata["iv"]);
        $concatedPassphrase = $passphrase.$salt;
        $md5 = array();
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        return json_decode($data, true);
    }
    
    /**
     * Encrypt value to a cryptojs compatiable json encoding string
     *
     * @param mixed $passphrase
     * @param mixed $value
     * @return string
     */
    public function cryptoJsAesEncrypt($passphrase, $value){
        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx.$passphrase.$salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv  = substr($salted, 32,16);
        $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
        $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
        return json_encode($data);
    }

    
}
