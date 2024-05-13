import 'dart:math';

import 'package:flutter/material.dart';

const primaryColor = Color(0xFFEF4533);
const primaryLinearGradient = LinearGradient(
  colors: [Color(0xFFEF4533), Color(0xFFFF0961)],
  transform: GradientRotation(pi),
);

final primaryBoxShadow = <BoxShadow>[
  BoxShadow(
    color: Color(0xFF09102A).withOpacity(0.12),
    offset: Offset(0.0, 4.0),
    blurRadius: 70.0,
  ),
];
const defaultGrey = Color(0xFF9B9B9B);
