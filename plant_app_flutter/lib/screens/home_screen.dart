import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../services/api_service.dart';
import 'scan_screen.dart';
import '../config.dart' as activeConfig;
import 'cart_screen.dart';
import 'dashboard_sidebar.dart';
import 'profile_screen.dart';
import 'order_history_screen.dart';
import 'product_detail_screen.dart';
import '../providers/auth_provider.dart';

class HomeScreen extends StatefulWidget {
  @override
  _HomeScreenState createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  List<dynamic> _allProducts = [];
  List<dynamic> _products = [];
  bool _isLoading = true;
  String _selectedCategory = 'All';
  int _cartItemCount = 0;
  final Set<dynamic> _cartingProducts = {};
  final Set<dynamic> _buyingProducts = {};

  @override
  void initState() {
    super.initState();
    _loadProducts();
    _refreshCartCount();
  }

  void _filterProducts(String category) {
    setState(() {
      _selectedCategory = category;
      if (category == 'All') {
        _products = _allProducts;
      } else {
        _products = _allProducts.where((p) => p['type'] == category).toList();
      }
    });
  }

  void _loadProducts() async {
    try {
      final res = await ApiService.get('products.php');
      if (res['status'] == 'success') {
        setState(() {
          _allProducts = res['data'];
          _products = _allProducts;
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _refreshCartCount() async {
    final userId = context.read<AuthProvider>().user?.id;
    if (userId == null || userId.isEmpty) {
      if (mounted) setState(() => _cartItemCount = 0);
      return;
    }

    try {
      final res = await ApiService.post('cart.php', {'action': 'view', 'user_id': userId});
      if (!mounted) return;
      if (res['status'] == 'success' && res['data'] is List) {
        int count = 0;
        for (final item in (res['data'] as List)) {
          final qty = int.tryParse('${item['quantity'] ?? 0}') ?? 0;
          count += qty;
        }
        setState(() => _cartItemCount = count);
      } else {
        setState(() => _cartItemCount = 0);
      }
    } catch (_) {
      if (mounted) setState(() => _cartItemCount = 0);
    }
  }

  Future<void> _addToCart(dynamic product, {bool openCartAfter = false}) async {
    final userId = context.read<AuthProvider>().user?.id;
    if (userId == null || userId.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please login to shop')),
      );
      return;
    }

    final productId = product['product_id'];
    setState(() {
      if (openCartAfter) {
        _buyingProducts.add(productId);
      } else {
        _cartingProducts.add(productId);
      }
    });

    try {
      final res = await ApiService.post('cart.php', {
        'action': 'add',
        'user_id': userId,
        'product_id': productId,
        'quantity': 1,
      });

      if (!mounted) return;

      if (res['status'] == 'success') {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(res['message'] ?? 'Added to cart')),
        );
        await _refreshCartCount();
        if (openCartAfter) {
          await Navigator.push(context, MaterialPageRoute(builder: (_) => CartScreen(autoCheckout: true)));
          await _refreshCartCount();
        }
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: ${res['message'] ?? 'Unable to add item'}')),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed: $e')),
        );
      }
    } finally {
      if (mounted) {
        setState(() {
          _cartingProducts.remove(productId);
          _buyingProducts.remove(productId);
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: DashboardSidebar(),
      appBar: AppBar(
        title: Row(
          children: [
            Icon(Icons.local_florist, color: Colors.white),
            SizedBox(width: 8),
            Text('Flora Ai', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
          ],
        ),
        backgroundColor: Colors.green[700],
        actions: [
          IconButton(
            icon: Stack(
              clipBehavior: Clip.none,
              children: [
                const Icon(Icons.shopping_cart),
                if (_cartItemCount > 0)
                  Positioned(
                    right: -8,
                    top: -8,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 2),
                      decoration: BoxDecoration(
                        color: Colors.red,
                        borderRadius: BorderRadius.circular(10),
                      ),
                      constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
                      child: Text(
                        _cartItemCount > 99 ? '99+' : '$_cartItemCount',
                        textAlign: TextAlign.center,
                        style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                      ),
                    ),
                  ),
              ],
            ),
            onPressed: () async {
              await Navigator.push(context, MaterialPageRoute(builder: (_) => CartScreen()));
              await _refreshCartCount();
            },
          ),
          IconButton(icon: Icon(Icons.person), onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => ProfileScreen()))),
          PopupMenuButton<String>(
            onSelected: (val) {
              if (val == 'orders') Navigator.push(context, MaterialPageRoute(builder: (_) => OrderHistoryScreen()));
            },
            itemBuilder: (context) => [
              PopupMenuItem(value: 'orders', child: Text('My Orders')),
            ],
          )
        ],
      ),
      body: _isLoading 
        ? Center(child: CircularProgressIndicator()) 
        : _allProducts.isEmpty
          ? Center(child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.wifi_off, size: 48, color: Colors.grey),
                SizedBox(height: 16),
                Text("Cannot connect to server.", style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
                Text("Make sure PC & Phone are on same Wi-Fi.\n(Check XAMPP is running)", textAlign: TextAlign.center, style: GoogleFonts.poppins(fontSize: 12, color: Colors.grey)),
                SizedBox(height: 16),
                ElevatedButton(
                  onPressed: () { setState(() => _isLoading = true); _loadProducts(); },
                  style: ElevatedButton.styleFrom(backgroundColor: Colors.green[700]), 
                  child: Text("Retry", style: TextStyle(color: Colors.white))
                )
              ]
            ))
          : Column(
            children: [
              Padding(
                padding: EdgeInsets.all(16),
                child: TextField(
                  decoration: InputDecoration(
                    hintText: 'Search plants...',
                    prefixIcon: Icon(Icons.search),
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                    filled: true,
                    fillColor: Colors.grey[200]
                  ),
                ),
              ),
              Container(
                height: 50,
                child: ListView(
                  scrollDirection: Axis.horizontal,
                  padding: EdgeInsets.symmetric(horizontal: 16),
                  children: [
                    _buildCategoryChip('All', 'All'),
                    _buildCategoryChip('Pesticides', 'Pesticide'),
                    _buildCategoryChip('Fertilizers', 'Fertilizer'),
                    _buildCategoryChip('Tools', 'Tool'),
                  ],
                ),
              ),
              Expanded(
                child: GridView.builder(
                  padding: EdgeInsets.all(16),
                  gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 2, 
                    childAspectRatio: 0.65,
                    crossAxisSpacing: 16,
                    mainAxisSpacing: 16
                  ),
                  itemCount: _products.length,
                  itemBuilder: (context, index) {
                    final product = _products[index];
                    return GestureDetector(
                      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => ProductDetailScreen(product: product))),
                      child: Card(
                        elevation: 4,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Expanded(
                              child: Hero(
                                tag: product['product_id'] ?? index,
                                child: Container(
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
                                    color: Colors.grey[200],
                                    image: DecorationImage(
                                      image: NetworkImage(
                                        activeConfig.Config.baseUrl.replaceAll('/api', '') + "/" + (product['image_url'] ?? product['image'] ?? '')
                                      ),
                                      fit: BoxFit.cover,
                                      onError: (exception, stackTrace) {}
                                    )
                                  ),
                                ),
                              ),
                            ),
                            Padding(
                              padding: EdgeInsets.all(8),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(product['name'], style: GoogleFonts.poppins(fontWeight: FontWeight.bold), maxLines: 1, overflow: TextOverflow.ellipsis),
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text('₹${product['price']}', style: GoogleFonts.poppins(color: Colors.green[700], fontWeight: FontWeight.bold)),
                                    ],
                                  )
                                  ,const SizedBox(height: 8),
                                  Row(
                                    children: [
                                      Expanded(
                                        child: OutlinedButton.icon(
                                          onPressed: (_cartingProducts.contains(product['product_id']) || _buyingProducts.contains(product['product_id']))
                                              ? null
                                              : () => _addToCart(product),
                                          icon: _cartingProducts.contains(product['product_id'])
                                              ? const SizedBox(width: 14, height: 14, child: CircularProgressIndicator(strokeWidth: 2))
                                              : const Icon(Icons.add_shopping_cart_outlined, size: 16),
                                          label: const Text('Cart'),
                                          style: OutlinedButton.styleFrom(
                                            padding: const EdgeInsets.symmetric(vertical: 8),
                                            foregroundColor: Colors.green[700],
                                            side: BorderSide(color: Colors.green[700]!),
                                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                          ),
                                        ),
                                      ),
                                      const SizedBox(width: 8),
                                      Expanded(
                                        child: ElevatedButton.icon(
                                          onPressed: (_cartingProducts.contains(product['product_id']) || _buyingProducts.contains(product['product_id']))
                                              ? null
                                              : () => _addToCart(product, openCartAfter: true),
                                          icon: _buyingProducts.contains(product['product_id'])
                                              ? const SizedBox(width: 14, height: 14, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                                              : const Icon(Icons.flash_on_outlined, size: 16),
                                          label: const Text('Buy'),
                                          style: ElevatedButton.styleFrom(
                                            padding: const EdgeInsets.symmetric(vertical: 8),
                                            backgroundColor: Colors.green[700],
                                            foregroundColor: Colors.white,
                                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                          ),
                                        ),
                                      ),
                                    ],
                                  )
                                ],
                              ),
                            )
                          ],
                        ),
                      ),
                    );
                  },
                ),
              ),
            ],
          ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          Navigator.push(context, MaterialPageRoute(builder: (_) => ScanScreen()));
        },
        backgroundColor: Colors.green[700],
        child: Icon(Icons.camera_alt),
      ),
    );
  }

  Widget _buildCategoryChip(String label, String dbType) {
    bool isSelected = _selectedCategory == dbType;
    return GestureDetector(
      onTap: () => _filterProducts(dbType),
      child: Container(
        margin: EdgeInsets.only(right: 8),
        child: Chip(
          label: Text(label, style: TextStyle(color: isSelected ? Colors.white : Colors.black, fontWeight: isSelected ? FontWeight.bold : FontWeight.normal)),
          backgroundColor: isSelected ? Colors.green[700] : Colors.grey[200],
        ),
      ),
    );
  }
}
