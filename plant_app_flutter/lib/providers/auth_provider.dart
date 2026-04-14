import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';

class AuthProvider with ChangeNotifier {
  UserModel? _user;
  bool _isLoading = false;

  UserModel? get user => _user;
  bool get isLoading => _isLoading;
  bool get isAuthenticated => _user != null;

  Future<bool> login(String email, String password) async {
    _setLoading(true);

    try {
      final response = await ApiService.post('login.php', {
        'email': email,
        'password': password,
      });

      if (response['status'] == 'success') {
        _user = UserModel.fromJson(response['user']);
        await _saveUserToPrefs();
        _setLoading(false);
        return true;
      }
      _setLoading(false);
      return false;
    } catch (e) {
      _setLoading(false);
      rethrow;
    }
  }

  Future<bool> register(String name, String email, String password) async {
    _setLoading(true);

    try {
      final response = await ApiService.post('register.php', {
        'name': name,
        'email': email,
        'password': password,
      });

      if (response['status'] == 'success') {
        _setLoading(false);
        return true;
      }
      _setLoading(false);
      return false;
    } catch (e) {
      _setLoading(false);
      rethrow;
    }
  }

  Future<void> logout() async {
    _user = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('user_data');
    notifyListeners();
  }

  Future<bool> tryAutoLogin() async {
    final prefs = await SharedPreferences.getInstance();
    if (!prefs.containsKey('user_data')) {
      return false;
    }

    final userData = jsonDecode(prefs.getString('user_data')!);
    _user = UserModel.fromJson(userData);
    notifyListeners();
    return true;
  }

  Future<void> _saveUserToPrefs() async {
    if (_user != null) {
      final prefs = await SharedPreferences.getInstance();
      final userData = jsonEncode(_user!.toJson());
      await prefs.setString('user_data', userData);
    }
  }

  void _setLoading(bool value) {
    _isLoading = value;
    notifyListeners();
  }
}
