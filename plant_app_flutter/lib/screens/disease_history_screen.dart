import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../services/api_service.dart';
import '../providers/auth_provider.dart';
import '../config.dart' as activeConfig;
import 'product_detail_screen.dart';

class DiseaseHistoryScreen extends StatefulWidget {
  @override
  _DiseaseHistoryScreenState createState() => _DiseaseHistoryScreenState();
}

class _DiseaseHistoryScreenState extends State<DiseaseHistoryScreen> {
  bool _isLoading = true;
  bool _hasError = false;
  List<dynamic> _history = [];

  @override
  void initState() {
    super.initState();
    _fetchHistory();
  }

  Future<void> _fetchHistory() async {
    final userId = Provider.of<AuthProvider>(context, listen: false).user?.id;
    if (userId == null) return;

    setState(() { _isLoading = true; _hasError = false; });

    try {
      final res = await ApiService.post('get_history.php', {'user_id': userId});
      if (res['status'] == 'success') {
        setState(() {
          _history = res['data'] ?? [];
          _isLoading = false;
        });
      } else {
        setState(() { _isLoading = false; _hasError = true; });
      }
    } catch (e) {
      if (mounted) setState(() { _isLoading = false; _hasError = true; });
    }
  }

  Future<void> _deleteRecord(int historyId) async {
    final userId = Provider.of<AuthProvider>(context, listen: false).user?.id;
    try {
      final res = await ApiService.post('delete_history.php', {'user_id': userId, 'id': historyId});
      if (res['status'] == 'success') {
        setState(() {
          _history.removeWhere((item) => item['id'] == historyId);
        });
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Record deleted')));
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Failed to delete: $e')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Disease History', style: GoogleFonts.poppins(color: Colors.white, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.green[700],
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator())
        : _hasError
          ? Center(child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.wifi_off, size: 48, color: Colors.grey),
                SizedBox(height: 16),
                Text("Cannot connect to server.", style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
                Text("Make sure PC & Phone are on same Wi-Fi.", style: GoogleFonts.poppins(fontSize: 12, color: Colors.grey)),
                SizedBox(height: 16),
                ElevatedButton(
                  onPressed: _fetchHistory,
                  style: ElevatedButton.styleFrom(backgroundColor: Colors.green[700]), 
                  child: Text("Retry", style: TextStyle(color: Colors.white))
                )
              ]
            ))
        : _history.isEmpty 
          ? Center(child: Text('No diagnostic history found.', style: GoogleFonts.poppins(fontSize: 16)))
          : ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: _history.length,
              itemBuilder: (context, index) {
                final item = _history[index];
                final List<dynamic> cures = item['cures'] ?? [];
                
                return Card(
                  margin: const EdgeInsets.only(bottom: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  elevation: 2,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      ListTile(
                        contentPadding: const EdgeInsets.all(16),
                        leading: ClipRRect(
                          borderRadius: BorderRadius.circular(8),
                          child: item['image_path'] != null 
                            ? Image.network(activeConfig.Config.baseUrl.replaceAll('/api', '') + '/' + item['image_path'], width: 60, height: 60, fit: BoxFit.cover,
                              errorBuilder: (c, e, s) => Container(width: 60, height: 60, color: Colors.grey[300], child: const Icon(Icons.image_not_supported)))
                            : Container(width: 60, height: 60, color: Colors.grey[300], child: const Icon(Icons.eco)),
                        ),
                        title: Text(item['plant_name'] ?? 'Unknown', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
                        subtitle: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const SizedBox(height: 4),
                            Text('Disease: ${item['disease_detected']}', style: GoogleFonts.poppins(color: Colors.red[400], fontWeight: FontWeight.bold)),
                            const SizedBox(height: 4),
                            Text('Date: ${item['scan_date']}', style: GoogleFonts.poppins(fontSize: 12, color: Colors.grey)),
                          ],
                        ),
                        trailing: IconButton(
                          icon: const Icon(Icons.delete, color: Colors.red),
                          onPressed: () {
                            showDialog(
                              context: context,
                              builder: (ctx) => AlertDialog(
                                title: Text('Delete Record?', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
                                content: Text('Are you sure you want to permanently delete this scan history?', style: GoogleFonts.poppins()),
                                actions: [
                                  TextButton(onPressed: () => Navigator.pop(ctx), child: Text('Cancel', style: GoogleFonts.poppins())),
                                  ElevatedButton(
                                    style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
                                    onPressed: () {
                                      Navigator.pop(ctx);
                                      _deleteRecord(item['id']);
                                    },
                                    child: Text('Delete', style: GoogleFonts.poppins(color: Colors.white)),
                                  )
                                ]
                              )
                            );
                          },
                        ),
                      ),
                      if (cures.isNotEmpty) Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text("Recommended Products:", style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.green[700])),
                            const SizedBox(height: 8),
                            SizedBox(
                              height: 100,
                              child: ListView.builder(
                                scrollDirection: Axis.horizontal,
                                itemCount: cures.length,
                                itemBuilder: (context, cureIndex) {
                                  final product = cures[cureIndex];
                                  return GestureDetector(
                                    onTap: () {
                                      Navigator.push(context, MaterialPageRoute(builder: (_) => ProductDetailScreen(product: product)));
                                    },
                                    child: Container(
                                      width: 220,
                                      margin: const EdgeInsets.only(right: 12),
                                      padding: const EdgeInsets.all(8),
                                      decoration: BoxDecoration(
                                        color: Colors.green[50]!.withOpacity(0.5),
                                        borderRadius: BorderRadius.circular(8),
                                        border: Border.all(color: Colors.green[200]!)
                                      ),
                                      child: Row(
                                        children: [
                                          ClipRRect(
                                            borderRadius: BorderRadius.circular(4),
                                            child: Image.network(
                                              activeConfig.Config.baseUrl.replaceAll('/api', '') + "/" + (product['image_url'] ?? ''),
                                              width: 50, height: 50, fit: BoxFit.cover,
                                              errorBuilder: (c,e,s) => Icon(Icons.shopping_bag, color: Colors.green)
                                            ),
                                          ),
                                          const SizedBox(width: 8),
                                          Expanded(
                                            child: Column(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              mainAxisAlignment: MainAxisAlignment.center,
                                              children: [
                                                Text(product['name'] ?? '', style: GoogleFonts.poppins(fontSize: 12, fontWeight: FontWeight.bold), maxLines: 2, overflow: TextOverflow.ellipsis),
                                                Text('₹${product['price']}', style: GoogleFonts.poppins(fontSize: 12, color: Colors.green[700], fontWeight: FontWeight.bold)),
                                              ]
                                            )
                                          )
                                        ]
                                      )
                                    )
                                  );
                                }
                              )
                            )
                          ]
                        )
                      )
                    ]
                  )
                );
              },
            ),
    );
  }
}
