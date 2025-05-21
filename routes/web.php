<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/dashboard', 'DashboardController@create')->name('dashboard.create');
Route::post('/dashboard', 'DashboardController@store')->name('dashboard.store');
Route::get('/dashboard/index', 'DashboardController@index')->name('dashboard.index');
Route::get('/dashboard/edit/{dashboard}', 'DashboardController@edit')->name('dashboard.edit');
Route::post('/dashboard/edit', 'DashboardController@update')->name('dashboard.update');
Route::get('/dashboard/destroy/{dashboard}', 'DashboardController@destroy')->name('dashboard.destroy');
Route::get('/dashboard/show/{dashboard}', 'DashboardController@show')->name('dashboard.show');

Route::get('/subdashboard', 'SubDashboardController@create')->name('subdashboard.create');
Route::post('/subdashboard', 'SubDashboardController@store')->name('subdashboard.store');
Route::get('/subdashboard/index', 'SubDashboardController@index')->name('subdashboard.index');
Route::get('/subdashboard/edit/{subDashboard}', 'SubDashboardController@edit')->name('subdashboard.edit');
Route::post('/subdashboard/edit', 'SubDashboardController@update')->name('subdashboard.update');
Route::get('/subdashboard/destroy/{subDashboard}', 'SubDashboardController@destroy')->name('subdashboard.destroy');
Route::get('/subdashboard/show/{subDashboard}', 'SubDashboardController@show')->name('subdashboard.show');

Route::get('/subsubdashboard', 'SubSubDashboardController@create')->name('subsubdashboard.create');
Route::post('/subsubdashboard', 'SubSubDashboardController@store')->name('subsubdashboard.store');
Route::get('/subsubdashboard/index', 'SubSubDashboardController@index')->name('subsubdashboard.index');
Route::get('/subsubdashboard/edit/{subSubDashboard}', 'SubSubDashboardController@edit')->name('subsubdashboard.edit');
Route::post('/subsubdashboard/edit', 'SubSubDashboardController@update')->name('subsubdashboard.update');
Route::get('/subsubdashboard/destroy/{subSubDashboard}', 'SubSubDashboardController@destroy')->name('subsubdashboard.destroy');
Route::get('/subsubdashboard/show/{subSubDashboard}', 'SubSubDashboardController@show')->name('subsubdashboard.show');

Route::get('/subsubsubdashboard', 'SubSubSubDashboardController@create')->name('subsubsubdashboard.create');
Route::post('/subsubsubdashboard', 'SubSubSubDashboardController@store')->name('subsubsubdashboard.store');
Route::get('/subsubsubdashboard/index', 'SubSubSubDashboardController@index')->name('subsubsubdashboard.index');
Route::get('/subsubsubdashboard/edit/{subSubSubDashboard}', 'SubSubSubDashboardController@edit')->name('subsubsubdashboard.edit');
Route::post('/subsubsubdashboard/edit', 'SubSubSubDashboardController@update')->name('subsubsubdashboard.update');
Route::get('/subsubsubdashboard/destroy/{subSubSubDashboard}', 'SubSubSubDashboardController@destroy')->name('subsubsubdashboard.destroy');
Route::get('/subsubsubdashboard/show/{subSubSubDashboard}', 'SubSubSubDashboardController@show')->name('subsubsubdashboard.show');

Route::get('/print/test/{demandeAchat}','PrintController@test');

Route::get('/print/da/{demandeAchat}','PrintController@printDemandeAchat');
Route::get('/print/da2/{demandeAchat}','PrintController@printDemandeAchat2');

Route::get('/print/df/{demandeFond}','PrintController@printDemandeFond');

Route::get('/print/dt/{travaux}','PrintController@printTravaux');

Route::get('/print/dp/{perdiem}','PrintController@printPerdiem');

Route::get('/session/get', 'SessionController@getSessionData');

Route::get('/session/set/{profil}', 'SessionController@storeSessionData');

Route::get('/session/remove', 'SessionController@deleteSessionData');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/profiles/create','ProfileController@create')->name('profiles.create');
Route::post('/profiles/create','ProfileController@store')->name('store');
Route::get('/profiles/{user}','ProfileController@show')->name('profiles.show');

Route::post('/retours/store','ValiderRetourController@store')->name('retours.validate');

Route::get('/requisitions/create','RequisitionController@create')->name('requisitions.create');
Route::post('/requisitions/create','RequisitionController@store')->name('requisitions.store');
Route::get('/requisitions/edit/{requisition}','RequisitionController@edit')->name('requisitions.edit');
Route::get('/requisitions/send/{requisition}','RequisitionController@edit')->name('requisitions.edit');
Route::post('/requisitions/edit','RequisitionController@update')->name('requisitions.update');
Route::get('/requisitions/index','RequisitionController@index')->name('requisitions.index');
Route::get('/requisitions/index_all','RequisitionController@index_all')->name('requisitions.index_all');
Route::get('/requisitions/show/{requisition}','RequisitionController@show')->name('requisitions.show');

Route::get('/requisitions/consommation/{famille?}/{code_structure?}/{article?}/{periode_debut?}/{periode_fin?}','RequisitionController@consommation')->name('requisitions.consommation');

Route::get('/prints/requisitions/print_consommation/{famille?}/{code_structure?}/{article?}/{periode_debut?}/{periode_fin?}','PrintController@print_consommation')->name('requisitions.print_consommation');

Route::get('/requisitions/crypt/{famille?}/{code_structure?}/{article?}/{periode_debut?}/{periode_fin?}','RequisitionController@crypt')->name('requisitions.crypt');

Route::post('/requisitions/crypt_post','RequisitionController@crypt_post')->name('requisitions.crypt_post');

Route::get('/requisitions/recap_consommation/{familles?}/{periode_debut?}/{periode_fin?}','RequisitionController@recap_consommation')->name('requisitions.recap_consommation');

Route::get('/prints/req/recap/{famille?}/{periode_debut?}/{periode_fin?}','PrintController@print_recap_consommation')->name('requisitions.print_recap_consommation');

Route::get('/requisitions/crypt_recap/{familles?}/{periode_debut?}/{periode_fin?}','RequisitionController@crypt_recap')->name('requisitions.crypt_recap');

Route::post('/requisitions/crypt_post_recap','RequisitionController@crypt_post_recap')->name('requisitions.crypt_post_recap');


Route::get('/valider_requisitions/create/{requisition}','ValiderRequisitionController@create')->name('valider_requisitions.create');
Route::get('/valider_requisitions/edit/{requisition}','ValiderRequisitionController@create')->name('valider_requisitions.create');
Route::get('/valider_requisitions/send/{requisition}','ValiderRequisitionController@create')->name('valider_requisitions.create');
Route::post('/valider_requisitions/create','ValiderRequisitionController@store')->name('valider_requisitions.store');

Route::get('/livraisons/index','LivraisonController@index')->name('livraisons.index');
Route::get('/livraisons/create/{requisition}','LivraisonController@create')->name('livraisons.create');
Route::get('/livraisons/distribution/{requisition}','LivraisonController@distribution')->name('livraisons.distribution');
Route::get('/livraisons/confirme/{requisition}','LivraisonController@create')->name('livraisons.create');
Route::post('/livraisons/create','LivraisonController@store')->name('livraisons.store');
Route::post('/livraisons/distribution','LivraisonController@distributions_store')->name('livraisons.distributions_store');
Route::get('/livraisons/reception/{requisition}','LivraisonController@reception')->name('livraisons.reception');
Route::post('/livraisons/reception','LivraisonController@receptions_store')->name('livraisons.receptions_store');

Route::get('/retours/index','RetourController@index')->name('retours.index');
Route::get('/retours/create/{livraison}','RetourController@create')->name('retours.create');
Route::post('/retours/create','RetourController@store')->name('retours.store');
Route::get('/retours/destroy/{retour}', 'RetourController@destroy')->name('retours.destroy');

Route::get('/valider_retours/index','ValiderRetourController@index')->name('valider_retours.index');
Route::get('/valider_retours/create/{retour}','ValiderRetourController@create')->name('valider_retours.create');
Route::post('/valider_retours/create','ValiderRetourController@store')->name('valider_retours.store');
Route::get('/valider_retours/destroy/{validerRetour}', 'ValiderRetourController@destroy')->name('valider_retours.destroy');

Route::get('/livraison_retours/index','LivraisonRetourController@index')->name('livraison_retours.index');
Route::get('/livraison_retours/create/{valider_retour}','LivraisonRetourController@create')->name('livraison_retours.create');
Route::post('/livraison_retours/create','LivraisonRetourController@store')->name('livraison_retours.store');
Route::get('/livraison_retours/destroy/{livraisonRetour}','LivraisonRetourController@destroy')->name('livraison_retours.destroy');

Route::get('/agents/create','AgentController@create')->name('agents.create');
Route::get('/agents/creates/{structure}','AgentController@creates')->name('agents.creates');
Route::post('/agents/create','AgentController@store')->name('agents.store');
Route::get('/agents/index','AgentController@index')->name('agents.index');
Route::get('/agents/destroy/{agent}','AgentController@destroy')->name('agents.destroy');
Route::get('/agents/edit/{agent}','AgentController@edit')->name('agents.edit');
Route::post('/agents/edit','AgentController@update')->name('agents.update');


Route::get('familles/create','FamilleController@create')->name('familles.create');
Route::post('familles/create','FamilleController@store')->name('familles.store');
Route::get('familles/edit/{famille}','FamilleController@edit')->name('familles.edit');
Route::post('familles/edit','FamilleController@update')->name('familles.update');
Route::get('/familles/destroy/{famille}','FamilleController@destroy')->name('familles.destroy');

Route::get('/articles/index','ArticleController@index')->name('articles.index');
Route::get('/articles/create','ArticleController@create')->name('articles.create');
Route::get('/articles/creates/{famille}','ArticleController@creates')->name('articles.creates');
Route::post('/articles/create','ArticleController@store')->name('articles.store');
Route::get('/articles/edit/{article}','ArticleController@edit')->name('articles.edit');
Route::post('/articles/edit','ArticleController@update')->name('articles.update');
Route::get('/articles/destroy/{article}','ArticleController@destroy')->name('articles.destroy');

Route::get('/depots/index','DepotController@index')->name('depots.index');
Route::get('/depots/create','DepotController@create')->name('depots.create');
Route::post('/depots/create','DepotController@store')->name('depots.store');
Route::get('/depots/edit/{depot}','DepotController@edit')->name('depots.edit');
Route::post('/depots/edit','DepotController@update')->name('depots.update');
Route::get('/depots/destroy/{depot}','DepotController@destroy')->name('depots.destroy');

Route::get('/magasins/index','MagasinController@index')->name('magasins.index');
Route::get('/magasins/create','MagasinController@create')->name('magasins.create');
Route::post('/magasins/create','MagasinController@store')->name('magasins.store');
Route::get('/magasins/edit/{magasin}','MagasinController@edit')->name('magasins.edit');
Route::post('/magasins/edit','MagasinController@update')->name('magasins.update');
Route::get('/magasins/destroy/{magasin}','MagasinController@destroy')->name('magasins.destroy');

Route::get('/magasin_stocks/create','MagasinStockController@create')->name('magasin_stocks.create');
Route::post('/magasin_stocks/create','MagasinStockController@store')->name('magasin_stocks.store');
Route::get('/magasin_stocks/index','MagasinStockController@index')->name('magasin_stocks.index');
Route::get('/magasin_stocks/edit/{magasinStock}','MagasinStockController@edit')->name('magasin_stocks.edit');
Route::get('/magasin_stocks/show/{magasinStock}','MagasinStockController@show')->name('magasin_stocks.show');
Route::get('/magasin_stocks/shows/{magasinStock}','MagasinStockController@shows')->name('magasin_stocks.shows');
Route::post('/magasin_stocks/edit','MagasinStockController@update')->name('magasin_stocks.update');
Route::get('/magasin_stocks/destroy/{magasinStock}','MagasinStockController@destroy')->name('magasin_stocks.destroy');

Route::get('/inventaires/create','InventaireController@create')->name('inventaires.create');
Route::get('/inventaires/edit/{inventaire}','InventaireController@edit')->name('inventaires.edit');
Route::post('/inventaires/create','InventaireController@store')->name('inventaires.store');
Route::get('/inventaires/index','InventaireController@index')->name('inventaires.index');
Route::post('/inventaires/edit','InventaireController@update')->name('inventaires.update');
Route::get('/inventaires/destroy/{inventaire}','InventaireController@destroy')->name('inventaires.destroy');

Route::get('/inventaire_articles/create/{inventaire}','InventaireArticleController@create')->name('inventaire_articles.create');
Route::post('/inventaire_articles/create','InventaireArticleController@store')->name('inventaire_articles.store');
Route::get('/inventaire_articles/index/{inventaire}','InventaireArticleController@index')->name('inventaire_articles.index');
Route::get('/inventaire_articles/edit/{inventaireArticle}','InventaireArticleController@edit')->name('inventaire_articles.edit');
Route::post('/inventaire_articles/edit','InventaireArticleController@update')->name('inventaire_articles.update');
Route::get('/inventaire_articles/destroy/{inventaireArticle}','InventaireArticleController@destroy')->name('inventaire_articles.destroy');
Route::get('/inventaire_articles/create','InventaireArticleController@create_inventaire_famille')->name('inventaire_articles.create_inventaire_famille');


Route::get('/demande_achats/create','DemandeAchatController@create')->name('demande_achats.create');

Route::post('/demande_achats/create','DemandeAchatController@store')->name('demande_achats.store');
Route::post('/demande_achats/cotation','DemandeAchatController@cotation_store')->name('demande_achats.cotation_store');

Route::get('/demande_achats/create/{famille?}/{code_structure?}/{code_gestion?}','DemandeAchatController@create_credit')->name('demande_achats.create_credit');

Route::get('/demande_achats/crypt/{famille?}/{code_structure?}/{code_gestion?}','DemandeAchatController@crypt')->name('demande_achats.crypt');

Route::get('/demande_achats/crypt2/{demandeAchat}/{famille?}/{code_structure?}/{code_gestion?}','DemandeAchatController@crypt2')->name('demande_achats.crypt2');



Route::get('/demande_achats/index','DemandeAchatController@index')->name('demande_achats.index');

Route::get('/demande_achats/edit/{demandeAchat}/{limited?}/{famille?}/{code_structure?}/{code_gestion?}','DemandeAchatController@edit')->name('demande_achats.edit');


Route::get('/demande_achats/send/{demandeAchat}/{limited?}/{famille?}/{code_structure?}/{code_gestion?}','DemandeAchatController@edit')->name('demande_achats.edit');

Route::post('/demande_achats/edit','DemandeAchatController@update')->name('demande_achats.update');
Route::get('/demande_achats/cotation/{demandeAchat}','DemandeAchatController@cotation')->name('demande_achats.cotation');
Route::get('/demande_achats/send_cotation/{demandeAchat}','DemandeAchatController@cotation')->name('demande_achats.cotation');
Route::get('/demande_achats/cotation_send_frs/{demandeAchat}','DemandeAchatController@cotation')->name('demande_achats.cotation');
Route::get('/demande_achats/show/{demandeAchat}','DemandeAchatController@show')->name('demande_achats.show');

Route::get('/valider_demande_achats/create/{demandeAchat}','ValiderDemandeAchatController@create')->name('valider_demande_achats.create');
Route::post('/valider_demande_achats/validat','ValiderDemandeAchatController@store')->name('valider_demande_achats.store');

Route::get('/cotation_fournisseurs/create/{demandeAchat}','CotationFournisseurController@create')->name('cotation_fournisseurs.create');
Route::post('/cotation_fournisseurs/create','CotationFournisseurController@store')->name('cotation_fournisseurs.store');
Route::get('/cotation_fournisseurs/create_bc/{cotationFournisseur}','CotationFournisseurController@create_bc')->name('cotation_fournisseurs.create_bc');
Route::get('/cotation_fournisseurs/show/{cotationFournisseur}','CotationFournisseurController@show')->name('cotation_fournisseurs.show');
Route::post('/cotation_fournisseurs/store_bc','CotationFournisseurController@store_bc')->name('cotation_fournisseurs.store_bc');

Route::get('/selection_adjudications/create/{cotationFournisseur}','SelectionAdjudicationController@create')->name('selection_adjudications.create');
Route::get('/selection_adjudications/liste/{demandeAchat}','SelectionAdjudicationController@liste')->name('selection_adjudications.liste');
Route::post('/selection_adjudications/create','SelectionAdjudicationController@store')->name('selection_adjudications.store');
Route::get('/selection_adjudications/index','SelectionAdjudicationController@index')->name('selection_adjudications.index');

Route::get('/adjudication_commandes/create/{selectionAdjudication}','AdjudicationCommandeController@create')->name('adjudication_commandes.create');

Route::get('/livraison_commandes/index/{cotationFournisseur}','LivraisonCommandeController@index')->name('livraison_commandes.index');

Route::get('/livraison_commandes/create/{cotationFournisseur}','LivraisonCommandeController@create')->name('livraison_commandes.create');

Route::get('/livraison_commandes/edit/{livraisonCommande}','LivraisonCommandeController@edit')->name('livraison_commandes.edit');

Route::get('/livraison_commandes/show/{livraisonCommande}','LivraisonCommandeController@show')->name('livraison_commandes.show');

Route::post('/livraison_commandes/edit','LivraisonCommandeController@update')->name('livraison_commandes.update');

Route::post('/livraison_commandes/create','LivraisonCommandeController@store')->name('livraison_commandes.store');

Route::get('/organisations/index','OrganisationController@index')->name('organisations.index');
Route::get('/organisations/create','OrganisationController@create')->name('organisations.create');
Route::get('/organisations/creates/{entnum}','OrganisationController@creates')->name('organisations.creates');
Route::post('/organisations/create','OrganisationController@store')->name('organisations.store');
Route::get('/organisations/edit/{organisation}','OrganisationController@edit')->name('organisations.edit');
Route::get('/organisations/edits/{organisation}/{entnum?}','OrganisationController@edits')->name('organisations.edits');
Route::post('/organisations/edit','OrganisationController@update')->name('organisations.update');

Route::get('/demande_fonds/index','DemandeFondController@index')->name('demande_fonds.index');
Route::get('/demande_fonds/test','DemandeFondController@test')->name('demande_fonds.test');
Route::post('/demande_fonds/store','DemandeFondController@store')->name('demande_fonds.store');
Route::get('/demande_fonds/show/{demandeFond}','DemandeFondController@show')->name('demande_fonds.show');
Route::post('/demande_fonds/edit','DemandeFondController@update')->name('demande_fonds.update');
Route::get('/demande_fonds/destroy/{demandeFond}','DemandeFondController@destroy')->name('demande_fonds.destroy');

Route::get('/demande_fonds/create','DemandeFondController@create')->name('demande_fonds.create');

Route::get('/demande_fonds/create/{famille?}/{code_structure?}/{code_gestion?}','DemandeFondController@create_credit')->name('demande_fonds.create_credit');

Route::get('/demande_fonds/edit/{demande_fonds}/{limited?}/{famille?}/{code_structure?}/{code_gestion?}','DemandeFondController@edit')->name('demande_fonds.edit');


Route::get('/demande_fonds/send/{demande_fonds}/{limited?}/{famille?}/{code_structure?}/{code_gestion?}','DemandeFondController@edit')->name('demande_fonds.edit');

Route::get('/demande_fonds/crypt/{famille?}/{code_structure?}/{code_gestion?}','DemandeFondController@crypt')->name('demande_fonds.crypt');

Route::get('/factures/create/{selectionAdjudication}','FactureController@create')->name('factures.create');
Route::post('/factures/store','FactureController@store')->name('factures.store');
Route::get('/factures/create_bc/{facture}','FactureController@create_bc')->name('factures.create_bc');
Route::post('/factures/store_bc','FactureController@store_bc')->name('factures.store_bc');



Route::get('/cotation_services/create/{demandeFond}','CotationServiceController@create')->name('cotation_services.create');
Route::post('/cotation_services/store','CotationServiceController@store')->name('cotation_services.store');
Route::get('/cotation_services/edit/{demandeFond}','CotationServiceController@edit')->name('cotation_services.edit');
Route::post('/cotation_services/edit','CotationServiceController@update')->name('cotation_services.update');

Route::get('/piece_jointes/show/{pieceJointe}','PieceJointeController@show')->name('piece_jointes.show');
Route::get('/bon_livraisons/shows/{bonLivraison}','PieceJointeController@shows')->name('piece_jointes.show');
Route::get('/pv_reception/{pieceJointeLivraison}','PieceJointeController@pv_reception')->name('piece_jointes.show');
Route::get('/taux_changes/index','TauxChangeController@index')->name('taux_changes.index');

Route::get('/comite/create','ComiteReceptionController@create')->name('comite_receptions.create');
Route::post('/comite/store','ComiteReceptionController@store')->name('comite_receptions.store');

Route::get('/travaux/index','TravauxController@index')->name('travaux.index');
Route::get('/travaux/create','TravauxController@create')->name('travaux.create');
Route::post('/travaux/store','TravauxController@store')->name('travaux.store');
Route::post('/travaux/edit','TravauxController@update')->name('travaux.update');
Route::get('/travaux/create/{famille?}/{code_structure?}/{code_gestion?}','TravauxController@create_credit')->name('travaux.create_credit');

Route::get('/travaux/edit/{travaux}/{limited?}/{famille?}/{code_structure?}/{code_gestion?}','TravauxController@edit')->name('travaux.edit');


Route::get('/travaux/send/{travaux}/{limited?}/{famille?}/{code_structure?}/{code_gestion?}','TravauxController@edit')->name('travaux.edit');


Route::get('/travaux/show/{travaux}','TravauxController@show')->name('travaux.show');

Route::get('/travaux/crypt/{famille?}/{code_structure?}/{code_gestion?}','TravauxController@crypt')->name('travaux.crypt');

Route::get('/print','PrintPdfController@print');

Route::get('/immobilisations/create','ImmobilisationController@create')->name('immobilisations.create');
Route::post('/immobilisations/create','ImmobilisationController@store')->name('immobilisations.store');
Route::get('/immobilisations/edit/{immobilisation}','ImmobilisationController@edit')->name('immobilisations.edit');
Route::get('/immobilisations/send/{immobilisation}','ImmobilisationController@edit')->name('immobilisations.edit');
Route::get('/immobilisations/stiker/{detail_immobilisation}','ImmobilisationController@stiker')->name('immobilisations.stiker');

Route::post('/immobilisations/edit','ImmobilisationController@update')->name('immobilisations.update');

Route::post('/immobilisations/update','ImmobilisationController@store_analyse')->name('immobilisations.store_analyse');

Route::get('/immobilisations/index','ImmobilisationController@index')->name('immobilisations.index');
Route::get('/immobilisations/show/{immobilisation}','ImmobilisationController@show')->name('immobilisations.show');

Route::get('/devises/create','DeviseController@create')->name('devises.create');
Route::post('/devises/create','DeviseController@store')->name('devises.store');

Route::get('/credit_budgetaires/create','CreditBudgetaireController@create')->name('credit_budgetaires.create');
Route::post('/credit_budgetaires/create','CreditBudgetaireController@store')->name('credit_budgetaires.store');

Route::get('/perdiems/index','PerdiemController@index')->name('perdiems.index');
//Route::get('/perdiems/create/{structure?}/{code_gestion?}','PerdiemController@create')->name('perdiems.create');

Route::get('/perdiems/create','PerdiemController@create')->name('perdiems.create');

Route::get('/perdiems/create_credit/{famille?}/{code_structure?}/{code_gestion?}','PerdiemController@create_credit')->name('perdiems.create_credit');

Route::post('/perdiems/store','PerdiemController@store')->name('perdiems.store');
//Route::get('/perdiems/edit/{perdiem}/{structure?}/{code_gestion?}','PerdiemController@edit')->name('perdiems.edit');

Route::get('/perdiems/edit/{perdiems}/{limited?}/{famille?}/{code_structure?}/{code_gestion?}','PerdiemController@edit')->name('perdiems.edit');

Route::get('/perdiems/show/{perdiem}','PerdiemController@show')->name('perdiems.show');
Route::post('/perdiems/edit','PerdiemController@update')->name('perdiems.update');
Route::get('/perdiems/destroy/{perdiem}','PerdiemController@destroy')->name('perdiems.destroy');

Route::get('/perdiems/crypt/{famille?}/{code_structure?}/{code_gestion?}','PerdiemController@crypt')->name('perdiems.crypt');


Route::get('/integration_inventaires/create','IntegrationInventaire@create')->name('integration_inventaires.create');

Route::get('/chargement_bon_de_commandes/create','IntegrationInventaire@creates')->name('chargement_bon_de_commandes.create');

Route::get('/pu/actualise','IntegrationInventaire@storePu')->name('integration_inventaires.storePu');

Route::get('/exercices/index','ExerciceController@index')->name('exercices.index');
Route::post('/exercices/index','ExerciceController@store')->name('exercices.store');
Route::get('/exercices/edit/{exercice}/{new_statut}','ExerciceController@edit')->name('exercices.edit');
Route::get('/exercices/show/{exercice}','ExerciceController@show')->name('exercices.show');
Route::post('/exercices/edit','ExerciceController@update')->name('exercices.update');
Route::get('/exercices/destroy/{exercice}','ExerciceController@destroy')->name('exercices.destroy');

Route::get('/public/documents/{typeOperation}/{exercice}/{fichier}','DocumentController@show')->name('document.show');

Route::get('/public/emargements/{ImageSignature}','DocumentController@ImageSignature')->name('document.ImageSignature');

Route::get('/mouvements/index/{magasinStock}','MouvementController@index')->name('mouvements.index');
Route::get('/mouvements/create','MouvementController@create')->name('mouvements.create');
Route::post('/mouvements/store','MouvementController@store')->name('mouvements.store');
Route::get('/mouvements/edit/{mouvement}','MouvementController@edit')->name('mouvements.edit');
Route::post('/mouvements/edit','MouvementController@update')->name('mouvements.update');

Route::get('/documents/{operations_id}/{type_operations_libelle}','SignatureController@viewDocumentSign');

Route::get('/demande_cotations/index','DemandeCotationController@index')->name('demande_cotations.index');
Route::get('/demande_cotations/create','DemandeCotationController@create')->name('demande_cotations.create');
Route::post('/demande_cotations/store','DemandeCotationController@store')->name('demande_cotations.store');
Route::get('/demande_cotations/edit/{demandeCotation}/{limited?}/{famille?}/{code_structure?}/{code_gestion?}','DemandeCotationController@edit')->name('demande_cotations.edit');

Route::get('/demande_cotations/show/{demandeCotation}','DemandeCotationController@show')->name('demande_cotations.show');

Route::post('/demande_cotations/edit','DemandeCotationController@update')->name('demande_cotations.update');
Route::get('/demande_cotations/crypt/{famille?}/{code_structure?}/{code_gestion?}','DemandeCotationController@crypt')->name('demande_cotations.crypt');

Route::get('/demande_cotations/create/{famille?}/{code_structure?}/{code_gestion?}','DemandeCotationController@create_credit')->name('demande_cotations.create_credit');


Route::get('/reponse_cotations/index/{demandeCotation}','ReponseCotationController@index')->name('reponse_cotations.index');
Route::get('/reponse_cotations/create/{demandeCotation}','ReponseCotationController@create')->name('reponse_cotations.create');
Route::post('/reponse_cotations/store','ReponseCotationController@store')->name('reponse_cotations.store');
Route::get('/reponse_cotations/edit/{reponseCotation}','ReponseCotationController@edit')->name('reponse_cotations.edit');
Route::get('/reponse_cotations/show/{reponseCotation}','ReponseCotationController@show')->name('reponse_cotations.show');
Route::post('/reponse_cotations/update','ReponseCotationController@update')->name('reponse_cotations.update');

Route::get('/etats/articles_compte_comptable/{famille?}/{date_debut?}/{date_fin?}','EtatController@articles_compte_comptable')->name('etats.articles_compte_comptable');

Route::get('/etats/crypt_articles_compte_comptable/{famille?}','EtatController@crypt_articles_compte_comptable')->name('etats.crypt_articles_compte_comptable');

Route::post('/etats/store','EtatController@store')->name('etats.store');
Route::get('/etats/print_articles_compte_comptable/{famille}/{date_debut?}/{date_fin?}','EtatController@print_articles_compte_comptable')->name('etats.print_articles_compte_comptable');


Route::get('/etats/stock/{famille?}/{depot?}/{article?}/{dateT?}','EtatController@stock')->name('etats.stock');
Route::get('/prints/etats/stock/{famille?}/{depot?}/{article?}/{dateT?}','EtatController@print_stock')->name('etats.print_stock');
Route::get('/etats/crypt/{famille?}/{depot?}/{article?}/{dateT?}','EtatController@crypt')->name('etats.crypt');
Route::post('/etats/crypt_post','EtatController@crypt_post')->name('etats.crypt_post');


Route::get('/etats/mouvement/{famille?}/{depot?}/{type_mouvement?}/{article?}/{periode_debut?}/{periode_fin?}','EtatController@mouvement')->name('etats.mouvement');
Route::get('/prints/etats/mouvement/{famille?}/{depot?}/{type_mouvement?}/{article?}/{periode_debut?}/{periode_fin?}','EtatController@print_mouvement')->name('etats.print_mouvement');
Route::get('/etats/crypt_mouvement/{famille?}/{depot?}/{type_mouvement?}/{article?}/{periode_debut?}/{periode_fin?}','EtatController@crypt_mouvement')->name('etats.crypt_mouvement');
Route::post('/etats/crypt_mouvement_post','EtatController@crypt_mouvement_post')->name('etats.crypt_mouvement_post');


Route::get('/mieux_disants/index/{demandeCotation}','MieuxDisantController@index')->name('mieux_disants.index');
Route::get('/mieux_disants/create/{reponseCotation}','MieuxDisantController@create')->name('mieux_disants.create');
Route::post('/mieux_disants/store','MieuxDisantController@store')->name('mieux_disants.store');
Route::get('/mieux_disants/edit/{mieuxDisant}','MieuxDisantController@edit')->name('mieux_disants.edit');
Route::get('/mieux_disants/show/{mieuxDisant}','MieuxDisantController@show')->name('mieux_disants.show');
Route::post('/mieux_disants/update','MieuxDisantController@update')->name('mieux_disants.update');

Route::get('/annulation/livraison_requisition/{requisitions_id}','AnnulationController@livraisonRequisition')->name('annulation.livraison_requisition');

