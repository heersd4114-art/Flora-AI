import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';
import '../config.dart' as activeConfig;
import 'add_product_screen.dart';

class AdminProductsScreen extends StatefulWidget {
  @override
  _AdminProductsScreenState createState() => _AdminProductsScreenState();
}

class _AdminProductsScreenState extends State<AdminProductsScreen> {
  bool _isLoading = true;
  List<dynamic> _products = [];

  @override
  void initState() {
    super.initState();
    _loadProducts();
  }

  Future<void> _loadProducts() async {
    setState(() => _isLoading = true);
    try {
      final res = await ApiService.get('admin_products.php'); // Assuming GET returns the list
      if (res['status'] == 'success' || res.containsKey('data')) {
        setState(() {
          _products = res['data'] ?? res;
          _isLoading = false;
        });
      } else {
        // Fallback if the endpoint is different
        final fallBackRes = await ApiService.get('products.php');
        setState(() {
          _products = fallBackRes['data'] ?? fallBackRes;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _deleteProduct(dynamic productId) async {
    try {
      final res = await ApiService.post('delete_product.php', {'product_id': productId});
      if (res['status'] == 'success') {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Product deleted successfully')));
        _loadProducts();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Failed: ${res['message']}')));
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Manage Catalog', style: GoogleFonts.poppins(color: Colors.white, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.blue[900],
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator())
        : ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: _products.length,
            itemBuilder: (context, index) {
              final product = _products[index];
              return Card(
                elevation: 2,
                margin: const EdgeInsets.only(bottom: 16),
                child: ListTile(
                  contentPadding: const EdgeInsets.all(16),
                  leading: ClipRRect(
                    borderRadius: BorderRadius.circular(8),
                    child: Image.network(
                      activeConfig.Config.baseUrl.replaceAll('/api', '') + "/" + (product['image_url'] ?? ''),
                      width: 60, height: 60, fit: BoxFit.cover,
                      errorBuilder: (ctx, err, stack) => Container(width: 60, height: 60, color: Colors.grey[300], child: const Icon(Icons.image_not_supported)),
                    ),
                  ),
                  title: Text(product['name'] ?? 'Unknown', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
                  subtitle: Text('₹${product['price']}', style: GoogleFonts.poppins(color: Colors.green[700], fontWeight: FontWeight.bold)),
                  trailing: IconButton(
                    icon: const Icon(Icons.delete, color: Colors.red),
                    onPressed: () {
                      showDialog(
                        context: context,
                        builder: (ctx) => AlertDialog(
                          title: const Text('Delete Product'),
                          content: const Text('Are you sure you want to delete this product?'),
                          actions: [
                            TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
                            TextButton(
                              onPressed: () {
                                Navigator.pop(ctx);
                                _deleteProduct(product['product_id']);
                              }, 
                              child: const Text('Delete', style: TextStyle(color: Colors.red))
                            ),
                          ],
                        )
                      );
                    },
                  ),
                ),
              );
            },
          ),
      floatingActionButton: FloatingActionButton(
        onPressed: () async {
          await Navigator.push(context, MaterialPageRoute(builder: (_) => AddProductScreen()));
          _loadProducts(); // Refresh after adding
        },
        backgroundColor: Colors.blue[900],
        foregroundColor: Colors.white,
        child: const Icon(Icons.add),
      ),
    );
  }
}
