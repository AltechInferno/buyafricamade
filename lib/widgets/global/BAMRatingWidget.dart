import 'package:flutter/material.dart';
import 'package:flutter_icons/flutter_icons.dart';
import 'package:flutter_rating_bar/flutter_rating_bar.dart';
import 'package:google_fonts/google_fonts.dart';


class BAMRatingWidget extends StatelessWidget {
  const BAMRatingWidget({Key key, @required this.rating}) : super(key: key);
  final  rating;
  @override
  Widget build(BuildContext context) {
     return buildRatingWithCountRow(rating);
  }
}


Row buildRatingWithCountRow( rating) {
  return Row(
    mainAxisSize: MainAxisSize.min,
    children: [
      RatingBar(
        itemSize: 14.0,
        ignoreGestures: true,
        initialRating: double.parse(rating),
        direction: Axis.horizontal,
        allowHalfRating: true,
        itemCount: 5,
        ratingWidget: RatingWidget(
          full: Icon(FontAwesome.star, color: Colors.amber),
          empty:
          Icon(FontAwesome.star, color: Color.fromRGBO(224, 224, 225, 1)),
        ),
        itemPadding: EdgeInsets.only(right: 4.0),
        onRatingUpdate: (rating) {
          print(rating);
        },
      ),
      Text("($rating)", style: GoogleFonts.poppins(
        fontSize: 10.0,
        fontWeight: FontWeight.w400,
        color: Color(0xFF9B9B9B)
      ),)
    ],
  );
}
