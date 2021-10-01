function Pagination() {
    this.current = 1;
    this.recordsPerPage = 6;
    return;
}

Pagination.prototype = {
    SetPage: function( pageN ) {
        if( pageN == this.current ) return;
        let direc = pageN < this.current ? "right" : "left";
        $("#main-content").hide("slide", { direction: direc }, 400, function() {
            $("#main-content").html("<div class='text-center my-5' id='to-be-removed-next'><div class='spinner-border text-primary me-3' role='status'><span class='visually-hidden'>Načítavam...</span></div>Načítavam stranu...</div>");
            $("#main-content").fadeIn();
            DataManager.GetPageRecent(pageN, (data)=> {
                $("#main-content").html("");
                $('html, body').animate({scrollTop:0}, 'fast');
                var pos = 0;
                var dataDownload = function(data, pos) {
                    assocId = data[pos];
                    DataManager.GetDataSpecial(assocId, (data, assocData)=>{
                        CardWorker.Generate(data.type, data.categ, data.startType, data.startNo, data.startTime, data.recordId, data.assocID, (out) => {
                            $('#to-be-removed').first().remove();
                            $('#main-content').prepend(out);
                            if( pos <= assocData.length ) dataDownload(assocData, pos+1);
                          }, preloading=()=>{
                            $('#main-content').prepend("<div class='text-center my-5' id='to-be-removed'><div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Načítavam...</span></div></div>");
                          });

                    }, data);
                }
                dataDownload(data, pos);
                this.data = data;
            });
        });
        this.current = pageN;
        this.ReloadBtns();
    },

    NextPage: function() {
        if( this.current < this.NumPages() ) this.SetPage( this.current+1 );
        return;
    },

    PrevPage: function() {
        if( this.current > 1 ) this.SetPage( this.current-1 );
        return;
    },

    ReloadBtns: function( removeLastCard=false ) {
        DataManager.GetAllRecent((data)=>{
            $( "#pagi-loader-wrapper" ).html("");
            $( "#pagi-loader-wrapper" ).fadeOut();
            if( data.length >= this.recordsPerPage && removeLastCard ) $("#main-content .one-card").last().remove();
            if( data.length > this.recordsPerPage ) {
                $( "#pagi-main-cotainer" ).show();
                $( ".pagi-btn" ).remove();
                this.data = data;
                this.current == 1 ? $("#pagi-prev-btn").hide() : $("#pagi-prev-btn").show();
                this.current == this.NumPages() ? $("#pagi-next-btn").hide() : $("#pagi-next-btn").show();
                this.ApplyBtnChanges();
            } else {
                $( "#pagi-main-cotainer" ).hide();
            }
        }, preloading=()=> {
            $( "#pagi-loader-wrapper" ).html("<div class='text-center'><div class='spinner-border text-primary me-2' role='status'><span class='visually-hidden'>Načítavam...</span></div>Obnovujem stránkovanie...</div>");
            $( "#pagi-loader-wrapper" ).fadeIn();
        });
    },

    ApplyBtnChanges: function() {
        if( this.data.length > this.recordsPerPage ) {
            for( let i = 1; i <= this.NumPages(); i++ ) {
                active = i == this.current ? "active" : "";
                $( "#pagi-next-btn" ).before(`<li class="page-item pagi-btn ${active}"><a class="page-link" href="javascript:Pagination.SetPage(${i})">${i}</a></li>`);
            }
        }
    },

    NumPages: function() {
        return Math.ceil( this.data.length / this.recordsPerPage );
    }
};