window.carousel = function(ubicaciones) {
    return {
        ubicaciones: ubicaciones,
        activeTab: 0,
        totalTabs: ubicaciones.length,
        next() {
            this.activeTab = (this.activeTab === this.totalTabs - 1) ? 0 : this.activeTab + 1;
        },
        prev() {
            this.activeTab = (this.activeTab === 0) ? this.totalTabs - 1 : this.activeTab - 1;
        }
    }
}


