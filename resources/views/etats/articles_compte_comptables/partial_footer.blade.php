@section('javascripts_datatable') 
    <!-- jQuery -->
    <script src="{{ asset('../plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('../plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('../plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('../plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('../plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('../plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('../dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('../dist/js/demo.js') }}"></script>
    <!-- Page specific script -->
    <script>
    $(function () {
        $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        });
    });
    </script>

    <script>

        editCompte = function(a){
            const saisie = document.getElementById('rf').value;
            const opts = document.getElementById('liste_rf').childNodes;

            
            for (var i = 0; i < opts.length; i++) {
                if (opts[i].value === saisie) {
                    
                    if(saisie != ''){
                    const block = saisie.split('->');
                    
                    var rf = block[0];
                    var df = block[1];
                    
                    }else{
                        df = "";
                        rf = "";
                    }

                    
                    if (rf === undefined) {
                        document.getElementById('rf').value = saisie;

                    }else{
                        
                        document.getElementById('rf').value = rf;

                        

                    }

                    if (df === undefined) {
                        document.getElementById('rf').value = saisie;

                        document.getElementById('df').value = "";

                    }else{

                        document.getElementById('df').value = df;


                        rf = document.getElementById('rf').value;
                        if (rf === '') {
                            rf = null;
                        }

                        document.location.replace('/etats/crypt_articles_compte_comptable/'+rf);

                    }
                    
                    break;
                }else{
                    document.getElementById('df').value = "";
                }
            }
        }

        editStructure = function(a){
            const saisie = document.getElementById('cst').value;
            const opts = document.getElementById('liste_cst').childNodes;

            
            for (var i = 0; i < opts.length; i++) {
                if (opts[i].value === saisie) {
                    
                    if(saisie != ''){
                    const block = saisie.split('->');
                    
                    var cst = block[0];
                    var nst = block[1];
                    
                    }else{
                        nst = "";
                        cst = "";
                    }

                    
                    if (cst === undefined) {
                        document.getElementById('cst').value = saisie;
                    }else{
                        
                        document.getElementById('cst').value = cst;

                    }

                    if (nst === undefined) {
                        document.getElementById('cst').value = saisie;

                        document.getElementById('nst').value = "";

                    }else{

                        document.getElementById('nst').value = nst;

                    }
                    
                    
                    break;
                }else{
                    document.getElementById('nst').value = "";
                }
            }
        }

        editCompteStock = function(a){
            const saisie = document.getElementById('rf').value;
            const opts = document.getElementById('liste_rf').childNodes;

            
            for (var i = 0; i < opts.length; i++) {
                if (opts[i].value === saisie) {
                    
                    if(saisie != ''){
                    const block = saisie.split('->');
                    
                    var rf = block[0];
                    var df = block[1];
                    
                    }else{
                        df = "";
                        rf = "";
                    }

                    
                    if (rf === undefined) {
                        document.getElementById('rf').value = saisie;

                    }else{
                        
                        document.getElementById('rf').value = rf;

                    }

                    if (df === undefined) {
                        document.getElementById('rf').value = saisie;

                        document.getElementById('df').value = "";

                    }else{

                        document.getElementById('df').value = df;


                        rf = document.getElementById('rf').value;
                        if (rf === '') {
                            rf = null;
                        }

                        cst = document.getElementById('cst').value;
                        if (cst != '') {}
                        document.location.replace('/etats/crypt/'+rf+'/'+cst);
                        

                    }
                    
                    break;
                }else{
                    document.getElementById('df').value = "";
                }
            }
        }

        editDepot = function(a){
            const saisie = document.getElementById('cst').value;
            const opts = document.getElementById('liste_depot').childNodes;

            
            for (var i = 0; i < opts.length; i++) {
                if (opts[i].value === saisie) {
                    
                    if(saisie != ''){
                    const block = saisie.split('->');
                    
                    var cst = block[0];
                    var nst = block[1];
                    
                    }else{
                        nst = "";
                        cst = "";
                    }

                    
                    if (cst === undefined) {
                        document.getElementById('cst').value = saisie;
                    }else{
                        
                        document.getElementById('cst').value = cst;

                    }

                    if (nst === undefined) {
                        document.getElementById('cst').value = saisie;

                        document.getElementById('nst').value = "";

                    }else{

                        document.getElementById('nst').value = nst;

                        rf = document.getElementById('rf').value;
                        if (rf != '') {
                            document.location.replace('/etats/crypt/'+rf+'/'+cst);
                        }

                    }
                    
                    
                    break;
                }else{
                    document.getElementById('nst').value = "";
                }
            }
        }

        editArticle = function(a){
            const saisie = document.getElementById('art').value;
            const opts = document.getElementById('liste_art').childNodes;

            
            for (var i = 0; i < opts.length; i++) {
                if (opts[i].value === saisie) {
                    
                    if(saisie != ''){
                    const block = saisie.split('->');
                    
                    var art = block[0];
                    var dart = block[1];
                    
                    }else{
                        dart = "";
                        art = "";
                    }

                    
                    if (art === undefined) {
                        document.getElementById('art').value = saisie;

                    }else{
                        
                        document.getElementById('art').value = art;

                    }

                    if (dart === undefined) {
                        document.getElementById('art').value = saisie;

                        document.getElementById('dart').value = "";

                    }else{

                        document.getElementById('dart').value = dart;

                    }
                    
                    break;
                }else{
                    document.getElementById('dart').value = "";
                }
            }
        }

        editCompteMouvement = function(a){
            const saisie = document.getElementById('rf').value;
            const opts = document.getElementById('liste_rf').childNodes;

            
            for (var i = 0; i < opts.length; i++) {
                if (opts[i].value === saisie) {
                    
                    if(saisie != ''){
                    const block = saisie.split('->');
                    
                    var rf = block[0];
                    var df = block[1];
                    
                    }else{
                        df = "";
                        rf = "";
                    }

                    
                    if (rf === undefined) {
                        document.getElementById('rf').value = saisie;

                    }else{
                        
                        document.getElementById('rf').value = rf;

                    }

                    if (df === undefined) {
                        document.getElementById('rf').value = saisie;

                        document.getElementById('df').value = "";

                    }else{

                        document.getElementById('df').value = df;


                        rf = document.getElementById('rf').value;
                        if (rf === '') {
                            rf = null;
                        }

                        cst = document.getElementById('cst').value;
                        type_mouvements_id = document.getElementById('type_mouvements_id').value;
                        if (type_mouvements_id != '') {
                            document.location.replace('/etats/crypt_mouvement/' + rf + '/' + cst + '/' + type_mouvements_id);
                        }
                        
                        

                    }
                    
                    break;
                }else{
                    document.getElementById('df').value = "";
                }
            }
        }

        editDepotMouvement = function(a){
            const saisie = document.getElementById('cst').value;
            const opts = document.getElementById('liste_depot').childNodes;

            
            for (var i = 0; i < opts.length; i++) {
                if (opts[i].value === saisie) {
                    
                    if(saisie != ''){
                    const block = saisie.split('->');
                    
                    var cst = block[0];
                    var nst = block[1];
                    
                    }else{
                        nst = "";
                        cst = "";
                    }

                    
                    if (cst === undefined) {
                        document.getElementById('cst').value = saisie;
                    }else{
                        
                        document.getElementById('cst').value = cst;

                    }

                    if (nst === undefined) {
                        document.getElementById('cst').value = saisie;

                        document.getElementById('nst').value = "";

                    }else{

                        document.getElementById('nst').value = nst;

                        rf = document.getElementById('rf').value;
                        type_mouvements_id = document.getElementById('type_mouvements_id').value;
                        if (rf != '' && type_mouvements_id != '') {
                            document.location.replace('/etats/crypt_mouvement/' + rf + '/' + cst + '/' + type_mouvements_id);
                        }

                    }
                    
                    
                    break;
                }else{
                    document.getElementById('nst').value = "";
                }
            }
        }

    </script>

@endsection