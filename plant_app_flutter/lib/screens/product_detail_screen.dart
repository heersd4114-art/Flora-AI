import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../services/api_service.dart';
import '../providers/auth_provider.dart';
import '../config.dart' as activeConfig;
import 'cart_screen.dart';

class ProductDetailScreen extends StatefulWidget {
  final dynamic product;
  const ProductDetailScreen({Key? key, required this.product}) : super(key: key);

  @override
  _ProductDetailScreenState createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  bool _isAdding = false;
  bool _isBuying = false;
  int _quantity = 1;
  int _cartItemCount = 0;

  @override
  void initState() {
    super.initState();
    _refreshCartCount();
  }

  Future<void> _refreshCartCount() async {
    final userId = Provider.of<AuthProvider>(context, listen: false).user?.id;
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

  Future<void> _addToCart({bool openCartAfter = false}) async {
    final userId = Provider.of<AuthProvider>(context, listen: false).user?.id;
    if (userId == null || userId.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: const Text('Please login to add items'),
          action: SnackBarAction(label: 'Login', onPressed: () {}),
        ));
        return;
    }

    setState(() {
      _isAdding = true;
      _isBuying = openCartAfter;
    });

    try {
      final res = await ApiService.post('cart.php', {
        'action': 'add', 
        'user_id': userId, 
        'product_id': widget.product['product_id'], 
        'quantity': _quantity
      });
      
      if (res['status'] == 'success') {
           ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(res['message'] ?? 'Added to cart')));
           await _refreshCartCount();
           if (openCartAfter && mounted) {
             await Navigator.push(context, MaterialPageRoute(builder: (_) => CartScreen(autoCheckout: true)));
             await _refreshCartCount();
           }
      } else {
           ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: ${res['message']}')));
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Failed: $e')));
    } finally {
      if (mounted) {
        setState(() {
          _isAdding = false;
          _isBuying = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back, color: Colors.black), 
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          IconButton(
            icon: Stack(
              clipBehavior: Clip.none,
              children: [
                const Icon(Icons.shopping_cart_outlined, color: Colors.black),
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
        ],
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Hero(
              tag: widget.product['product_id'] ?? widget.product.hashCode,
              child: Container(
                height: 400,
                decoration: BoxDecoration(
                  borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(32), bottomRight: Radius.circular(32)),
                  color: Colors.grey[200],
                  image: DecorationImage(
                    image: NetworkImage(
                      activeConfig.Config.baseUrl.replaceAll('/api', '') + "/" + (widget.product['image_url'] ?? widget.product['image'] ?? '')
                    ),
                    fit: BoxFit.cover,
                    onError: (err, stack) {}
                  )
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(widget.product['name'], style: GoogleFonts.poppins(fontSize: 28, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 8),
                  Text('₹${widget.product['price']}', style: GoogleFonts.poppins(fontSize: 24, fontWeight: FontWeight.bold, color: Colors.green[700])),
                  const SizedBox(height: 24),
                  Text('Description', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 8),
                  Text(widget.product['description'] ?? 'No description available.', style: GoogleFonts.poppins(fontSize: 14, color: Colors.grey[700], height: 1.5)),
                  const SizedBox(height: 24),
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.green[50],
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: Colors.green.shade100),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('Quantity', style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.w600)),
                        Row(
                          children: [
                            IconButton(
                              onPressed: _quantity > 1 ? () => setState(() => _quantity--) : null,
                              icon: const Icon(Icons.remove_circle_outline),
                            ),
                            Text('$_quantity', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold)),
                            IconButton(
                              onPressed: () => setState(() => _quantity++),
                              icon: const Icon(Icons.add_circle_outline),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            )
          ],
        ),
      ),
      bottomNavigationBar: Padding(
        padding: const EdgeInsets.all(24),
        child: Row(
          children: [
            Expanded(
              child: OutlinedButton(
                onPressed: _isAdding || _isBuying ? null : () => _addToCart(),
                style: OutlinedButton.styleFrom(
                  side: BorderSide(color: Colors.green.shade700),
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                ),
                child: _isAdding && !_isBuying
                    ? const SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(strokeWidth: 2),
                      )
                    : Text('Add to Cart', style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.green[700])),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: ElevatedButton(
                onPressed: _isAdding || _isBuying ? null : () => _addToCart(openCartAfter: true),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.green[700],
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16))
                ),
                child: (_isAdding && _isBuying)
                    ? const SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                      )
                    : Text('Buy Now', style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
